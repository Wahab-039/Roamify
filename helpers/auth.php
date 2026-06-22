<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once dirname(__DIR__) . '/models/User.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user === null) {
        $user = User::findById((int) $_SESSION['user_id']);
    }

    return $user ?: null;
}

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    static $admin = null;
    if ($admin === null) {
        $stmt = Database::connection()->prepare('SELECT id, name, email, role FROM admins WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['admin_id']]);
        $admin = $stmt->fetch() ?: null;
    }

    return $admin;
}

function require_login(): void
{
    if (!current_user()) {
        flash('warning', 'Please log in to continue.');
        redirect('login.php');
    }
}

function require_guest(): void
{
    if (current_user()) {
        redirect('index.php');
    }
}

function require_admin(): void
{
    if (!current_admin()) {
        flash('warning', 'Admin access required.');
        redirect('admin/login.php');
    }
}

