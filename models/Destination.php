<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class Destination
{
    public static function search(array $filters, int $page = 1, int $perPage = 9): array
    {
        [$where, $params] = self::buildWhere($filters);
        $offset = max(0, ($page - 1) * $perPage);

        $sql = "SELECT d.*,
                    (SELECT image_path FROM destination_images di WHERE di.destination_id = d.id ORDER BY di.is_primary DESC, di.id ASC LIMIT 1) AS primary_image,
                    (SELECT COUNT(*) FROM favorites f WHERE f.destination_id = d.id) AS favorites_count,
                    COALESCE((SELECT AVG(r.rating) FROM reviews r WHERE r.destination_id = d.id), 0) AS average_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.destination_id = d.id) AS reviews_count
                FROM destinations d
                $where
                ORDER BY d.featured DESC, d.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = Database::connection()->prepare($sql);
        $position = 1;
        foreach ($params as $value) {
            $stmt->bindValue($position++, $value);
        }
        $stmt->bindValue($position++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($position, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countSearch(array $filters): int
    {
        [$where, $params] = self::buildWhere($filters);
        $stmt = Database::connection()->prepare("SELECT COUNT(*) FROM destinations d $where");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public static function featured(int $limit = 6): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT d.*,
                (SELECT image_path FROM destination_images di WHERE di.destination_id = d.id ORDER BY di.is_primary DESC, di.id ASC LIMIT 1) AS primary_image,
                COALESCE((SELECT AVG(r.rating) FROM reviews r WHERE r.destination_id = d.id), 0) AS average_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.destination_id = d.id) AS reviews_count
             FROM destinations d
             WHERE d.featured = 1
             ORDER BY d.created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT d.*,
                (SELECT image_path FROM destination_images di WHERE di.destination_id = d.id ORDER BY di.is_primary DESC, di.id ASC LIMIT 1) AS primary_image,
                COALESCE((SELECT AVG(r.rating) FROM reviews r WHERE r.destination_id = d.id), 0) AS average_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.destination_id = d.id) AS reviews_count
             FROM destinations d WHERE d.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function images(int $destinationId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM destination_images WHERE destination_id = ? ORDER BY is_primary DESC, id ASC');
        $stmt->execute([$destinationId]);

        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO destinations
                (title, country, city, category, description, location, latitude, longitude, estimated_cost, best_time, travel_tips, featured)
             VALUES
                (:title, :country, :city, :category, :description, :location, :latitude, :longitude, :estimated_cost, :best_time, :travel_tips, :featured)'
        );
        $stmt->execute(self::payload($data));

        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $payload = self::payload($data);
        $payload['id'] = $id;
        $stmt = Database::connection()->prepare(
            'UPDATE destinations SET
                title = :title,
                country = :country,
                city = :city,
                category = :category,
                description = :description,
                location = :location,
                latitude = :latitude,
                longitude = :longitude,
                estimated_cost = :estimated_cost,
                best_time = :best_time,
                travel_tips = :travel_tips,
                featured = :featured
             WHERE id = :id'
        );
        $stmt->execute($payload);
    }

    public static function addImage(int $destinationId, string $imagePath, bool $isPrimary = false): void
    {
        if ($isPrimary) {
            $reset = Database::connection()->prepare('UPDATE destination_images SET is_primary = 0 WHERE destination_id = ?');
            $reset->execute([$destinationId]);
        }

        $stmt = Database::connection()->prepare(
            'INSERT INTO destination_images (destination_id, image_path, is_primary) VALUES (?, ?, ?)'
        );
        $stmt->execute([$destinationId, $imagePath, $isPrimary ? 1 : 0]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM destinations WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function deleteImage(int $imageId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM destination_images WHERE id = ?');
        $stmt->execute([$imageId]);
    }

    public static function count(): int
    {
        return (int) Database::connection()->query('SELECT COUNT(*) FROM destinations')->fetchColumn();
    }

    public static function categories(): array
    {
        $stmt = Database::connection()->query('SELECT DISTINCT category FROM destinations WHERE category IS NOT NULL AND category <> "" ORDER BY category');

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private static function buildWhere(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(d.title LIKE ? OR d.country LIKE ? OR d.city LIKE ? OR d.description LIKE ?)';
            $term = '%' . $filters['q'] . '%';
            array_push($params, $term, $term, $term, $term);
        }

        if (!empty($filters['category'])) {
            $where[] = 'd.category = ?';
            $params[] = $filters['category'];
        }

        if (!empty($filters['max_cost'])) {
            $where[] = 'd.estimated_cost <= ?';
            $params[] = (float) $filters['max_cost'];
        }

        return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $params];
    }

    private static function payload(array $data): array
    {
        return [
            'title' => trim((string) ($data['title'] ?? '')),
            'country' => trim((string) ($data['country'] ?? '')),
            'city' => trim((string) ($data['city'] ?? '')),
            'category' => trim((string) ($data['category'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'location' => trim((string) ($data['location'] ?? '')),
            'latitude' => trim((string) ($data['latitude'] ?? '')) === '' ? null : (float) $data['latitude'],
            'longitude' => trim((string) ($data['longitude'] ?? '')) === '' ? null : (float) $data['longitude'],
            'estimated_cost' => (float) ($data['estimated_cost'] ?? 0),
            'best_time' => trim((string) ($data['best_time'] ?? '')),
            'travel_tips' => trim((string) ($data['travel_tips'] ?? '')),
            'featured' => !empty($data['featured']) ? 1 : 0,
        ];
    }
}
