<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class Favorite
{
    public static function exists(int $userId, int $destinationId): bool
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ? AND destination_id = ?');
        $stmt->execute([$userId, $destinationId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function toggle(int $userId, int $destinationId): bool
    {
        if (self::exists($userId, $destinationId)) {
            $stmt = Database::connection()->prepare('DELETE FROM favorites WHERE user_id = ? AND destination_id = ?');
            $stmt->execute([$userId, $destinationId]);
            return false;
        }

        $stmt = Database::connection()->prepare('INSERT INTO favorites (user_id, destination_id) VALUES (?, ?)');
        $stmt->execute([$userId, $destinationId]);

        return true;
    }

    public static function forUser(int $userId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT d.*,
                f.created_at AS saved_at,
                (SELECT image_path FROM destination_images di WHERE di.destination_id = d.id ORDER BY di.is_primary DESC, di.id ASC LIMIT 1) AS primary_image
             FROM favorites f
             INNER JOIN destinations d ON d.id = f.destination_id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM favorites')->fetchColumn();
    }
}

