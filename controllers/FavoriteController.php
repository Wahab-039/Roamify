<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/models/Favorite.php';

final class FavoriteController
{
    public static function toggle(): void
    {
        require_login();
        verify_csrf();

        $destinationId = (int) ($_POST['destination_id'] ?? 0);
        if ($destinationId <= 0) {
            self::respond(false, 'Invalid destination.');
        }

        $saved = Favorite::toggle((int) current_user()['id'], $destinationId);
        self::respond(true, $saved ? 'Destination saved.' : 'Destination removed.', ['saved' => $saved]);
    }

    private static function respond(bool $ok, string $message, array $extra = []): void
    {
        $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => $ok, 'message' => $message] + $extra);
            exit;
        }

        flash($ok ? 'success' : 'danger', $message);
        redirect($_SERVER['HTTP_REFERER'] ?? 'destinations.php');
    }
}

