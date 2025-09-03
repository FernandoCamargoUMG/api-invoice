<?php

namespace InvoiceSystem\Models;

use InvoiceSystem\Core\Database;
use PDO;

class CustomerModel
{
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM customers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create($data): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address']]);
        return ['id' => $db->lastInsertId()];
    }

    public static function update($id, $data): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address'], $id]);
        return ['success' => true];
    }

    public static function delete($id): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }
}
