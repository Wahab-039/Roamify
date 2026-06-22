<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class Message
{
    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO messages (name, email, subject, body, ip_address) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            trim((string) $data['name']),
            strtolower(trim((string) $data['email'])),
            trim((string) $data['subject']),
            trim((string) $data['body']),
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function all(): array
    {
        return Database::connection()
            ->query('SELECT * FROM messages ORDER BY created_at DESC')
            ->fetchAll();
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM messages')->fetchColumn();
    }
}

