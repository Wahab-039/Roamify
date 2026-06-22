<?php
require_once dirname(__DIR__, 2) . '/helpers/auth.php';
$pageTitle = $pageTitle ?? APP_NAME;
$user = current_user();
$admin = current_admin();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if (!empty($useLeaflet)): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('index.php') ?>">Roamify</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="<?= url('index.php') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('destinations.php') ?>">Destinations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('about.php') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('contact.php') ?>">Contact</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('favorites.php') ?>">Favorites</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('plans.php') ?>">Plans</a></li>
                    <li class="nav-item dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?= e($user['name']) ?></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('budget.php') ?>">New budget plan</a></li>
                            <?php if ($admin): ?>
                                <li><a class="dropdown-item" href="<?= url('admin/index.php') ?>">Admin panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('logout.php') ?>">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('login.php') ?>">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= url('register.php') ?>">Create account</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <?php require __DIR__ . '/alerts.php'; ?>
</div>
