<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/models/Destination.php';

$pageTitle = 'Smart Travel Planner';
$featured = Destination::featured(6);
require __DIR__ . '/views/partials/header.php';
?>
<header class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center min-vh-70 g-5">
            <div class="col-lg-7">
                <span class="badge text-bg-light mb-3">Smart tourism portal</span>
                <h1 class="display-4 fw-bold text-white">Plan better trips with destinations, budgets, and live weather in one place.</h1>
                <p class="lead text-white-75 mt-3">Search curated travel spots, save favorites, estimate trip costs, and prepare with practical weather insights before you leave.</p>
                <form action="<?= url('destinations.php') ?>" class="hero-search mt-4" method="get">
                    <div class="input-group input-group-lg">
                        <input type="search" name="q" class="form-control" placeholder="Search city, country, or experience" aria-label="Search destinations">
                        <button class="btn btn-accent" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="trip-widget">
                    <h2 class="h5 fw-bold mb-3">Quick Trip Estimate</h2>
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="metric-label">Avg. saved trips</span>
                            <strong>4.8 days</strong>
                        </div>
                        <div class="col-6">
                            <span class="metric-label">Planner mode</span>
                            <strong>Budget smart</strong>
                        </div>
                        <div class="col-12">
                            <a href="<?= url('budget.php') ?>" class="btn btn-primary w-100">Open budget planner</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main>
    <section class="container py-5">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
            <div>
                <p class="section-kicker mb-1">Featured destinations</p>
                <h2 class="fw-bold mb-0">Popular places to start exploring</h2>
            </div>
            <a href="<?= url('destinations.php') ?>" class="btn btn-outline-primary">View all</a>
        </div>
        <div class="row g-4">
            <?php foreach ($featured as $destination): ?>
                <div class="col-md-6 col-xl-4">
                    <?php require __DIR__ . '/views/destination_card.php'; ?>
                </div>
            <?php endforeach; ?>
            <?php if (!$featured): ?>
                <div class="col-12">
                    <div class="empty-state">No destinations yet. Import the database seed or add destinations from the admin panel.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="experience-band py-5">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <p class="section-kicker mb-1">Built for planning</p>
                    <h2 class="fw-bold">From inspiration to estimated cost.</h2>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        <div class="col-md-4"><div class="feature-tile"><strong>Discover</strong><span>Search by destination, category, and budget.</span></div></div>
                        <div class="col-md-4"><div class="feature-tile"><strong>Save</strong><span>Build a shortlist of favorite places.</span></div></div>
                        <div class="col-md-4"><div class="feature-tile"><strong>Plan</strong><span>Create and manage trip budget plans.</span></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

