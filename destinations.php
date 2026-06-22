<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/DestinationController.php';
require_once __DIR__ . '/models/Favorite.php';

$pageTitle = 'Destinations';
$data = DestinationController::listing();
extract($data);
require __DIR__ . '/views/partials/header.php';
?>
<main class="container py-5">
    <div class="page-heading mb-4">
        <p class="section-kicker mb-1">Explore</p>
        <h1 class="fw-bold mb-2">Destinations</h1>
        <p class="text-muted mb-0">Filter travel spots by experience and estimated cost.</p>
    </div>

    <form class="filter-bar mb-4" method="get">
        <div class="row g-3">
            <div class="col-lg-5">
                <input type="search" class="form-control" name="q" value="<?= e($filters['q']) ?>" placeholder="Search destination, city, or country">
            </div>
            <div class="col-md-4 col-lg-3">
                <select class="form-select" name="category">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category) ?>" <?= selected($filters['category'], $category) ?>><?= e($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 col-lg-2">
                <input type="number" min="0" class="form-control" name="max_cost" value="<?= e($filters['max_cost']) ?>" placeholder="Max PKR">
            </div>
            <div class="col-md-4 col-lg-2 d-grid">
                <button class="btn btn-primary" type="submit">Apply</button>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0"><?= (int) $total ?> destination<?= $total === 1 ? '' : 's' ?> found</p>
        <a href="<?= url('destinations.php') ?>" class="small">Clear filters</a>
    </div>

    <div class="row g-4">
        <?php foreach ($destinations as $destination): ?>
            <div class="col-md-6 col-xl-4">
                <?php require __DIR__ . '/views/destination_card.php'; ?>
            </div>
        <?php endforeach; ?>
        <?php if (!$destinations): ?>
            <div class="col-12"><div class="empty-state">No matching destinations. Try a wider search.</div></div>
        <?php endif; ?>
    </div>

    <?php if ($pages > 1): ?>
        <nav class="mt-5" aria-label="Destination pagination">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <?php $query = http_build_query(array_merge($_GET, ['page' => $i])); ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('destinations.php?' . $query) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

