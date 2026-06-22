<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class BudgetPlan
{
    public static function create(int $userId, array $data): int
    {
        $days = max(1, (int) ($data['days'] ?? 1));
        $hotel = max(0, (float) ($data['hotel_cost'] ?? 0));
        $food = max(0, (float) ($data['food_cost'] ?? 0));
        $transport = max(0, (float) ($data['transport_cost'] ?? 0));
        $misc = max(0, (float) ($data['misc_cost'] ?? 0));
        $total = $days * ($hotel + $food + $transport + $misc);

        $stmt = Database::connection()->prepare(
            'INSERT INTO budget_plans
                (user_id, destination_id, title, days, hotel_cost, food_cost, transport_cost, misc_cost, total_cost)
             VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            !empty($data['destination_id']) ? (int) $data['destination_id'] : null,
            trim((string) ($data['title'] ?? 'Trip plan')),
            $days,
            $hotel,
            $food,
            $transport,
            $misc,
            $total,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public static function forUser(int $userId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT bp.*, d.title AS destination_title, d.city, d.country
             FROM budget_plans bp
             LEFT JOIN destinations d ON d.id = bp.destination_id
             WHERE bp.user_id = ?
             ORDER BY bp.created_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function deleteForUser(int $id, int $userId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM budget_plans WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM budget_plans')->fetchColumn();
    }
}

