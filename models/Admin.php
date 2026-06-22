<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class Admin
{
    public static function authenticate(string $email, string $password): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower($email)]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password'])) {
            return null;
        }

        return $admin;
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    }
}

