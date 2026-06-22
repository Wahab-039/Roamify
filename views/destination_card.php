<?php
require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/models/Favorite.php';
$currentUser = current_user();
$isSaved = $currentUser ? Favorite::exists((int) $currentUser['id'], (int) $destination['id']) : false;
$description = function_exists('mb_strimwidth')
    ? mb_strimwidth($destination['description'], 0, 115, '...')
    : substr($destination['description'], 0, 115) . (strlen($destination['description']) > 115 ? '...' : '');
$averageRating = (float) ($destination['average_rating'] ?? 0);
$reviewsCount = (int) ($destination['reviews_count'] ?? 0);
?>
<article class="destination-card h-100">
    <img src="<?= e(destination_image($destination['primary_image'] ?? null)) ?>" alt="<?= e($destination['title']) ?>" loading="lazy">
    <div class="destination-card-body">
        <div class="d-flex justify-content-between gap-3 align-items-start">
            <div>
                <span class="badge text-bg-light"><?= e($destination['category']) ?></span>
                <h3 class="h5 fw-bold mt-2 mb-1"><?= e($destination['title']) ?></h3>
                <p class="text-muted small mb-0"><?= e($destination['city']) ?>, <?= e($destination['country']) ?></p>
                <div class="rating-mini mt-2" aria-label="<?= number_format($averageRating, 1) ?> out of 5 stars">
                    <span><?= str_repeat('★', (int) round($averageRating)) ?><?= str_repeat('☆', 5 - (int) round($averageRating)) ?></span>
                    <small><?= $reviewsCount ? number_format($averageRating, 1) . ' (' . $reviewsCount . ')' : 'No reviews' ?></small>
                </div>
            </div>
            <form action="<?= url('favorites.php') ?>" method="post" class="favorite-form">
                <?= csrf_field() ?>
                <input type="hidden" name="destination_id" value="<?= (int) $destination['id'] ?>">
                <button class="favorite-btn <?= $isSaved ? 'saved' : '' ?>" type="submit" title="Save destination" aria-label="Save destination">
                    <span><?= $isSaved ? 'Saved' : 'Save' ?></span>
                </button>
            </form>
        </div>
        <p class="mt-3 mb-3 card-description"><?= e($description) ?></p>
        <div class="d-flex justify-content-between align-items-center">
            <strong>PKR <?= number_format((float) $destination['estimated_cost']) ?></strong>
            <a href="<?= url('destination.php?id=' . (int) $destination['id']) ?>" class="btn btn-sm btn-outline-primary">Details</a>
        </div>
    </div>
</article>
