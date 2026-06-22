<?php
declare(strict_types=1);

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Destination.php';
require_once __DIR__ . '/Favorite.php';
require_once __DIR__ . '/BudgetPlan.php';
require_once __DIR__ . '/Message.php';
require_once __DIR__ . '/Review.php';

final class Dashboard
{
    public static function stats(): array
    {
        return [
            'users' => User::count(),
            'destinations' => Destination::count(),
            'favorites' => Favorite::count(),
            'plans' => BudgetPlan::count(),
            'messages' => Message::count(),
        ];
    }
}
