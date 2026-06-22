<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/models/Destination.php';
require_once dirname(__DIR__) . '/models/Weather.php';
require_once dirname(__DIR__) . '/models/Review.php';

final class DestinationController
{
    public static function listing(): array
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'category' => trim((string) ($_GET['category'] ?? '')),
            'max_cost' => trim((string) ($_GET['max_cost'] ?? '')),
        ];
        $perPage = 9;
        $total = Destination::countSearch($filters);

        return [
            'filters' => $filters,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'destinations' => Destination::search($filters, $page, $perPage),
            'categories' => Destination::categories(),
        ];
    }

    public static function detail(int $id): array
    {
        $destination = Destination::find($id);
        if (!$destination) {
            flash('danger', 'Destination not found.');
            redirect('destinations.php');
        }

        return [
            'destination' => $destination,
            'images' => Destination::images($id),
            'weather' => Weather::forDestination($destination),
            'reviews' => Review::forDestination($id),
            'reviewSummary' => Review::summary($id),
        ];
    }
}
