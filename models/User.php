<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class User
{
    public static function create(string $name, string $email, string $password): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, "user", "active")'
        );
        $stmt->execute([
            $name,
            strtolower($email),
            password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower($email)]);

        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, name, email, role, status, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function authenticate(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if (!$user || $user['status'] !== 'active' || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    public static function paginate(int $page = 1, int $perPage = 12): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $stmt = Database::connection()->prepare(
            'SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public static function updateStatus(int $id, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
}

