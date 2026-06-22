<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/controllers/AdminController.php';
require_once dirname(__DIR__) . '/models/User.php';

if (is_post()) {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        AdminController::deleteUser();
    }
    AdminController::updateUserStatus();
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$users = User::paginate($page, 20);
$total = User::count();
$pages = max(1, (int) ceil($total / 20));
$pageTitle = 'Manage Users';
require dirname(__DIR__) . '/views/partials/admin_header.php';
?>
<div class="mb-4">
    <p class="section-kicker mb-1">Accounts</p>
    <h1 class="h3 fw-bold mb-0">Manage users</h1>
</div>
<div class="admin-panel">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['role']) ?></td>
                        <td><span class="badge <?= $user['status'] === 'active' ? 'text-bg-success' : 'text-bg-danger' ?>"><?= e($user['status']) ?></span></td>
                        <td><?= e(date('M d, Y', strtotime($user['created_at']))) ?></td>
                        <td class="text-end">
                            <form method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="status">
                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                <input type="hidden" name="status" value="<?= $user['status'] === 'active' ? 'blocked' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-primary" type="submit"><?= $user['status'] === 'active' ? 'Block' : 'Activate' ?></button>
                            </form>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="6" class="text-muted">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
        <nav class="mt-3" aria-label="User pagination">
            <ul class="pagination mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= url('admin/users.php?page=' . $i) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php require dirname(__DIR__) . '/views/partials/admin_footer.php'; ?>

