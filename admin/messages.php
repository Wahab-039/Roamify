<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/controllers/AdminController.php';
require_once dirname(__DIR__) . '/models/Message.php';

if (is_post()) {
    AdminController::deleteMessage();
}

$messages = Message::all();
$pageTitle = 'Messages';
require dirname(__DIR__) . '/views/partials/admin_header.php';
?>
<div class="mb-4">
    <p class="section-kicker mb-1">Inbox</p>
    <h1 class="h3 fw-bold mb-0">Contact messages</h1>
</div>
<div class="row g-4">
    <?php foreach ($messages as $message): ?>
        <div class="col-lg-6">
            <article class="admin-panel h-100">
                <div class="d-flex justify-content-between gap-3 align-items-start mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-1"><?= e($message['subject']) ?></h2>
                        <p class="small text-muted mb-0"><?= e($message['name']) ?> · <?= e($message['email']) ?></p>
                    </div>
                    <span class="badge text-bg-light"><?= e(date('M d', strtotime($message['created_at']))) ?></span>
                </div>
                <p><?= nl2br(e($message['body'])) ?></p>
                <form method="post" onsubmit="return confirm('Delete this message?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
            </article>
        </div>
    <?php endforeach; ?>
    <?php if (!$messages): ?>
        <div class="col-12"><div class="admin-panel text-muted">No messages yet.</div></div>
    <?php endif; ?>
</div>
<?php require dirname(__DIR__) . '/views/partials/admin_footer.php'; ?>

