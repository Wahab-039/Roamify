<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/models/Dashboard.php';
require_once dirname(__DIR__) . '/models/Message.php';

$pageTitle = 'Dashboard';
$stats = Dashboard::stats();
$messages = array_slice(Message::all(), 0, 5);
require dirname(__DIR__) . '/views/partials/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="section-kicker mb-1">Overview</p>
        <h1 class="h3 fw-bold mb-0">Dashboard</h1>
    </div>
    <a href="<?= url('admin/destinations.php?action=create') ?>" class="btn btn-primary">Add destination</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($stats as $label => $count): ?>
        <div class="col-sm-6 col-xl">
            <div class="stat-card">
                <span><?= e(ucfirst($label)) ?></span>
                <strong><?= (int) $count ?></strong>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="admin-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Recent messages</h2>
        <a href="<?= url('admin/messages.php') ?>" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Name</th><th>Subject</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= e($message['name']) ?></td>
                        <td><?= e($message['subject']) ?></td>
                        <td><?= e(date('M d, Y', strtotime($message['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$messages): ?>
                    <tr><td colspan="3" class="text-muted">No messages yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require dirname(__DIR__) . '/views/partials/admin_footer.php'; ?>

