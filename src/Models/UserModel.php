<?php

namespace InvoiceSystem\Models;

use InvoiceSystem\Core\Database;
use PDO;

class UserModel
{
    public static function getByEmail($email): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create($data): array
    {
        $db = Database::getConnection();
        $hashedPassword = md5($data['password']);
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['email'], $hashedPassword, $data['role']]);
        return ['id' => $db->lastInsertId()];
    }

    public static function update($id, $data): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['email'], $data['role'], md5($data['password']), $id]);
        return ['success' => true];
    }

    public static function delete($id): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }
}
