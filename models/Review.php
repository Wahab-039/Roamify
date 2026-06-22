<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class Review
{
    public static function upsert(int $userId, int $destinationId, int $rating, string $comment): void
    {
        $rating = min(5, max(1, $rating));
        $stmt = Database::connection()->prepare(
            'INSERT INTO reviews (user_id, destination_id, rating, comment)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), updated_at = NOW()'
        );
        $stmt->execute([$userId, $destinationId, $rating, trim($comment)]);
    }

    public static function forDestination(int $destinationId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT r.*, u.name AS user_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.destination_id = ?
             ORDER BY r.updated_at DESC'
        );
        $stmt->execute([$destinationId]);

        return $stmt->fetchAll();
    }

    public static function forUserDestination(int $userId, int $destinationId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM reviews WHERE user_id = ? AND destination_id = ? LIMIT 1'
        );
        $stmt->execute([$userId, $destinationId]);

        return $stmt->fetch() ?: null;
    }

    public static function summary(int $destinationId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT COALESCE(AVG(rating), 0) AS average_rating, COUNT(*) AS reviews_count
             FROM reviews
             WHERE destination_id = ?'
        );
        $stmt->execute([$destinationId]);

        $summary = $stmt->fetch() ?: ['average_rating' => 0, 'reviews_count' => 0];

        return [
            'average_rating' => (float) $summary['average_rating'],
            'reviews_count' => (int) $summary['reviews_count'],
        ];
    }
}

