<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/FavoriteController.php';
require_once __DIR__ . '/models/Favorite.php';

if (is_post()) {
    FavoriteController::toggle();
}

require_login();
$pageTitle = 'Favorites';
$favorites = Favorite::forUser((int) current_user()['id']);
require __DIR__ . '/views/partials/header.php';
?>
<main class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <p class="section-kicker mb-1">Saved places</p>
            <h1 class="fw-bold mb-0">Favorite destinations</h1>
        </div>
        <a class="btn btn-outline-primary align-self-md-end" href="<?= url('destinations.php') ?>">Find more</a>
    </div>
    <div class="row g-4">
        <?php foreach ($favorites as $destination): ?>
            <div class="col-md-6 col-xl-4">
                <?php require __DIR__ . '/views/destination_card.php'; ?>
            </div>
        <?php endforeach; ?>
        <?php if (!$favorites): ?>
            <div class="col-12">
                <div class="empty-state">You have not saved any destinations yet.</div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

