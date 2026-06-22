<?php
require_once __DIR__ . '/config/bootstrap.php';
$pageTitle = 'About';
require __DIR__ . '/views/partials/header.php';
?>
<main class="container py-5">
    <div class="row g-5 align-items-center">
        <div class="col-lg-6">
            <p class="section-kicker mb-1">About Roamify</p>
            <h1 class="fw-bold">A practical travel platform for better semester-project depth.</h1>
            <p class="lead-sm text-muted">Roamify combines destination discovery, account-based favorite saving, budget planning, contact management, admin CRUD, image uploads, and weather caching into one cohesive PHP and MySQL application.</p>
        </div>
        <div class="col-lg-6">
            <img class="about-img" src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80" alt="Travel planning desk">
        </div>
    </div>
    <div class="row g-4 mt-4">
        <div class="col-md-4"><div class="feature-tile light"><strong>Secure by design</strong><span>PDO, CSRF, escaped output, secure sessions, and password hashing.</span></div></div>
        <div class="col-md-4"><div class="feature-tile light"><strong>Admin-ready</strong><span>Dashboard, destination CRUD, image upload, users, and messages.</span></div></div>
        <div class="col-md-4"><div class="feature-tile light"><strong>User-focused</strong><span>Favorites and reusable trip plans make the app feel complete.</span></div></div>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

