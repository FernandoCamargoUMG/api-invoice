<?php

namespace InvoiceSystem\Models;

use InvoiceSystem\Core\Database;
use PDO;

class ProductModel {
    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create($data): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['description'], $data['price'], $data['stock']]);
        return ['id' => $db->lastInsertId()];
    }

    public static function update($id, $data): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['description'], $data['price'], $data['stock'], $id]);
        return ['success' => true];
    }

    public static function delete($id): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }
}