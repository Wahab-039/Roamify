<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/DestinationController.php';
require_once __DIR__ . '/controllers/ReviewController.php';
require_once __DIR__ . '/models/Favorite.php';

$reviewErrors = [];
if (is_post()) {
    $reviewErrors = ReviewController::store();
}

$id = (int) ($_GET['id'] ?? 0);
$data = DestinationController::detail($id);
$destination = $data['destination'];
$images = $data['images'];
$weather = $data['weather'];
$reviews = $data['reviews'];
$reviewSummary = $data['reviewSummary'];
$userReview = current_user() ? Review::forUserDestination((int) current_user()['id'], (int) $destination['id']) : null;
$selectedRating = (string) ($_SESSION['_old']['rating'] ?? ($userReview['rating'] ?? '5'));
$weatherIcon = 'cloudy';
if ($weather && !empty($weather['icon'])) {
    $allowedWeatherIcons = ['clear', 'partly-cloudy', 'cloudy', 'rain', 'storm', 'snow', 'fog'];
    $weatherIcon = in_array($weather['icon'], $allowedWeatherIcons, true) ? $weather['icon'] : 'cloudy';
}
$useLeaflet = !empty($destination['latitude']) && !empty($destination['longitude']);
$pageTitle = $destination['title'];
require __DIR__ . '/views/partials/header.php';
?>
<main>
    <section class="detail-hero">
        <img src="<?= e(destination_image($destination['primary_image'] ?? null)) ?>" alt="<?= e($destination['title']) ?>">
        <div class="container">
            <div class="detail-hero-copy">
                <span class="badge text-bg-light"><?= e($destination['category']) ?></span>
                <h1 class="display-5 fw-bold text-white mt-3"><?= e($destination['title']) ?></h1>
                <p class="lead text-white-75"><?= e($destination['city']) ?>, <?= e($destination['country']) ?></p>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <h2 class="h4 fw-bold">Overview</h2>
                <p class="lead-sm"><?= nl2br(e($destination['description'])) ?></p>
                <div class="row g-3 mt-3">
                    <?php foreach ($images as $image): ?>
                        <div class="col-sm-6">
                            <img class="gallery-img" src="<?= e(destination_image($image['image_path'])) ?>" alt="<?= e($destination['title']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <h2 class="h4 fw-bold mt-5">Travel tips</h2>
                <p><?= nl2br(e($destination['travel_tips'])) ?></p>
            </div>
            <aside class="col-lg-4">
                <div class="sticky-lg-top detail-sidebar">
                    <div class="info-panel mb-3">
                        <h3 class="h5 fw-bold">Trip Snapshot</h3>
                        <div class="info-row"><span>Location</span><strong><?= e($destination['location']) ?></strong></div>
                        <div class="info-row"><span>Estimated cost</span><strong>PKR <?= number_format((float) $destination['estimated_cost']) ?></strong></div>
                        <div class="info-row"><span>Best time</span><strong><?= e($destination['best_time']) ?></strong></div>
                        <div class="info-row"><span>Rating</span><strong><?= $reviewSummary['reviews_count'] ? number_format($reviewSummary['average_rating'], 1) . '/5' : 'No reviews' ?></strong></div>
                    </div>
                    <?php if (!empty($destination['latitude']) && !empty($destination['longitude'])): ?>
                        <div class="info-panel mb-3">
                            <h3 class="h5 fw-bold">Map</h3>
                            <div
                                id="destinationMap"
                                class="destination-map"
                                data-lat="<?= e((string) $destination['latitude']) ?>"
                                data-lng="<?= e((string) $destination['longitude']) ?>"
                                data-title="<?= e($destination['title']) ?>"
                                data-location="<?= e($destination['city'] . ', ' . $destination['country']) ?>"
                            ></div>
                        </div>
                    <?php endif; ?>
                    <div class="info-panel mb-3">
                        <h3 class="h5 fw-bold">Weather</h3>
                        <?php if ($weather): ?>
                            <div class="weather-box">
                                <?php if (!empty($weather['icon'])): ?>
                                    <img src="<?= e(asset('img/weather/' . $weatherIcon . '.svg')) ?>" alt="<?= e($weather['weather_condition']) ?>">
                                <?php endif; ?>
                                <div>
                                    <strong><?= number_format((float) $weather['temperature'], 1) ?>°C</strong>
                                    <span><?= e(ucfirst($weather['weather_condition'])) ?></span>
                                    <small>Humidity <?= (int) $weather['humidity'] ?>%</small>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Weather is unavailable right now. Add an OpenWeatherMap API key to enable live conditions.</p>
                        <?php endif; ?>
                    </div>
                    <a class="btn btn-primary w-100 mb-2" href="<?= url('budget.php?destination_id=' . (int) $destination['id']) ?>">Plan budget</a>
                    <a class="btn btn-outline-primary w-100" href="<?= url('destinations.php') ?>">Back to destinations</a>
                </div>
            </aside>
        </div>
    </section>

    <section class="reviews-band py-5" id="reviews">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <p class="section-kicker mb-1">Traveler reviews</p>
                    <h2 class="fw-bold">Ratings from Roamify users</h2>
                    <div class="rating-summary mt-3">
                        <strong><?= number_format($reviewSummary['average_rating'], 1) ?></strong>
                        <span><?= str_repeat('★', (int) round($reviewSummary['average_rating'])) ?><?= str_repeat('☆', 5 - (int) round($reviewSummary['average_rating'])) ?></span>
                        <small><?= (int) $reviewSummary['reviews_count'] ?> review<?= $reviewSummary['reviews_count'] === 1 ? '' : 's' ?></small>
                    </div>

                    <?php if (current_user()): ?>
                        <form class="form-shell mt-4 needs-validation" method="post" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="destination_id" value="<?= (int) $destination['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label" for="rating">Your rating</label>
                                <select class="form-select <?= has_error($reviewErrors, 'rating') ?>" id="rating" name="rating" required>
                                    <?php for ($star = 5; $star >= 1; $star--): ?>
                                        <option value="<?= $star ?>" <?= selected($selectedRating, (string) $star) ?>><?= $star ?> star<?= $star === 1 ? '' : 's' ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="invalid-feedback"><?= first_error($reviewErrors, 'rating') ?: 'Choose a rating.' ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="comment">Your review</label>
                                <textarea class="form-control <?= has_error($reviewErrors, 'comment') ?>" id="comment" name="comment" rows="4" required><?= old('comment', (string) ($userReview['comment'] ?? '')) ?></textarea>
                                <div class="invalid-feedback"><?= first_error($reviewErrors, 'comment') ?: 'Write at least 10 characters.' ?></div>
                            </div>
                            <button class="btn btn-primary" type="submit"><?= $userReview ? 'Update review' : 'Post review' ?></button>
                        </form>
                    <?php else: ?>
                        <div class="empty-state mt-4">Log in to write a review for this destination.</div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-7">
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <article class="review-card">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <strong><?= e($review['user_name']) ?></strong>
                                        <div class="rating-mini mt-1"><span><?= str_repeat('★', (int) $review['rating']) ?><?= str_repeat('☆', 5 - (int) $review['rating']) ?></span></div>
                                    </div>
                                    <small class="text-muted"><?= e(date('M d, Y', strtotime($review['updated_at']))) ?></small>
                                </div>
                                <p class="mb-0 mt-3"><?= nl2br(e($review['comment'])) ?></p>
                            </article>
                        <?php endforeach; ?>
                        <?php if (!$reviews): ?>
                            <div class="empty-state">No reviews yet. Be the first traveler to share feedback.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>
