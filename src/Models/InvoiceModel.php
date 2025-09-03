<?php

namespace InvoiceSystem\Models;

use InvoiceSystem\Core\Database;
use PDO;

class InvoiceModel
{
    public static function getAll($limit = 10, $offset = 0): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM invoices LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($invoice) {
            // Obtener los items de la factura
            $stmtItems = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
            $stmtItems->execute([$id]);
            $invoice['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            // Calcular subtotal e impuesto dinámicamente para mostrar el desglose
            $taxRate = isset($_ENV['TAX_RATE']) ? floatval($_ENV['TAX_RATE']) : 0.12;
            $totales = self::calcularTotales($invoice['items'], $taxRate);
            $invoice['subtotal'] = $totales['subtotal'];
            $invoice['tax'] = $totales['tax'];
            $invoice['tax_rate'] = $taxRate;
            $invoice['total'] = $totales['total'];
        }
        return $invoice ?: null;
    }

    public static function create($data): array
    {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $taxRate = isset($_ENV['TAX_RATE']) ? floatval($_ENV['TAX_RATE']) : 0.12;
            $totales = self::calcularTotales($data['items'], $taxRate);

            $stmt = $db->prepare("INSERT INTO invoices (customer_id, user_id, total, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['customer_id'],
                $data['user_id'],
                $totales['total'],
                $data['status'] ?? 'pending'
            ]);
            $invoiceId = $db->lastInsertId();

            foreach ($data['items'] as $item) {
                $stmtItem = $db->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmtItem->execute([
                    $invoiceId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }

            // Después de insertar la factura y los items
            self::manejarPago($invoiceId, $totales['total'], $data['status'] ?? 'pending');

            $db->commit();
            return [
                'id' => $invoiceId,
                'subtotal' => $totales['subtotal'],
                'tax' => $totales['tax'],
                'total' => $totales['total'],
                'tax_rate' => $taxRate
            ];
        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function update($id, $data): array
    {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $taxRate = isset($_ENV['TAX_RATE']) ? floatval($_ENV['TAX_RATE']) : 0.12;
            $totales = ['subtotal' => 0, 'tax' => 0, 'total' => 0];
            if (isset($data['items'])) {
                $totales = self::calcularTotales($data['items'], $taxRate);
            } else if (isset($data['total'])) {
                $totales['subtotal'] = floatval($data['total']) / (1 + $taxRate);
                $totales['tax'] = $totales['subtotal'] * $taxRate;
                $totales['total'] = $totales['subtotal'] + $totales['tax'];
            }

            // Actualizar la factura
            $stmt = $db->prepare("UPDATE invoices SET customer_id = ?, user_id = ?, total = ?, status = ? WHERE id = ?");
            $stmt->execute([
                $data['customer_id'],
                $data['user_id'],
                $totales['total'],
                $data['status'] ?? 'pending',
                $id
            ]);

            // Actualizar los items si se envían
            if (isset($data['items'])) {
                $stmtItems = $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
                $stmtItems->execute([$id]);
                foreach ($data['items'] as $item) {
                    $stmtItem = $db->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmtItem->execute([
                        $id,
                        $item['product_id'],
                        $item['quantity'],
                        $item['price']
                    ]);
                }
            }
            // Después de actualizar la factura y los items
            self::manejarPago($id, $totales['total'], $data['status'] ?? 'pending');
            
            $db->commit();
            return [
                'success' => true,
                'subtotal' => $totales['subtotal'],
                'tax' => $totales['tax'],
                'total' => $totales['total'],
                'tax_rate' => $taxRate
            ];
        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function delete($id): array
    {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            // Eliminar pagos asociados
            $stmtPayments = $db->prepare("DELETE FROM payments WHERE invoice_id = ?");
            $stmtPayments->execute([$id]);

            $stmtItems = $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
            $stmtItems->execute([$id]);
            $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $db->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Método privado para calcular subtotal, impuesto y total
    private static function calcularTotales($items, $taxRate)
    {
        $subtotal = 0;
        $tax = 0;
        foreach ($items as $item) {
            $itemSubtotal = $item['price'] / (1 + $taxRate);
            $itemTax = $item['price'] - $itemSubtotal;
            $subtotal += $itemSubtotal * $item['quantity'];
            $tax += $itemTax * $item['quantity'];
        }
        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal + $tax, 2)
        ];
    }

    private static function manejarPago($invoiceId, $total, $status)
    {
        $db = Database::getConnection();

        // Si la factura está pagada, inserta el pago si no existe
        if ($status === 'paid') {
            $stmt = $db->prepare("SELECT id FROM payments WHERE invoice_id = ?");
            $stmt->execute([$invoiceId]);
            if (!$stmt->fetch()) {
                $stmtInsert = $db->prepare("INSERT INTO payments (invoice_id, amount) VALUES (?, ?)");
                $stmtInsert->execute([$invoiceId, $total]);
            }
        }
        // Si la factura está cancelada, elimina el pago si existe
        elseif ($status === 'canceled') {
            $stmtDelete = $db->prepare("DELETE FROM payments WHERE invoice_id = ?");
            $stmtDelete->execute([$invoiceId]);
        }
        // Si está en pending, no hace nada
    }
}
