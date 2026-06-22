<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/validation.php';
require_once dirname(__DIR__) . '/models/Review.php';

final class ReviewController
{
    public static function store(): array
    {
        require_login();
        verify_csrf();

        $errors = validate_required($_POST, [
            'destination_id' => 'Destination',
            'rating' => 'Rating',
            'comment' => 'Review',
        ]);

        $destinationId = (int) ($_POST['destination_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));

        if ($destinationId <= 0) {
            add_error($errors, 'destination_id', 'Invalid destination.');
        }

        if ($rating < 1 || $rating > 5) {
            add_error($errors, 'rating', 'Choose a rating from 1 to 5.');
        }

        if (strlen($comment) < 10) {
            add_error($errors, 'comment', 'Review must be at least 10 characters.');
        }

        if ($errors) {
            set_old($_POST);
            return $errors;
        }

        Review::upsert((int) current_user()['id'], $destinationId, $rating, $comment);
        clear_old();
        flash('success', 'Your review has been saved.');
        redirect('destination.php?id=' . $destinationId . '#reviews');
    }
}

