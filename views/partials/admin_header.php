<?php
require_once dirname(__DIR__, 2) . '/helpers/auth.php';
require_admin();
$pageTitle = $pageTitle ?? 'Admin';
$admin = current_admin();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?> Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="admin-body">
<nav class="navbar navbar-expand-lg navbar-dark admin-nav">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="<?= url('admin/index.php') ?>">Roamify Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/destinations.php') ?>">Destinations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/users.php') ?>">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/messages.php') ?>">Messages</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/logout.php') ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
</div>
<main class="container-fluid px-4 py-4">

