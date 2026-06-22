<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/ContactController.php';

$errors = [];
if (is_post()) {
    $errors = ContactController::store();
}

$pageTitle = 'Contact';
require __DIR__ . '/views/partials/header.php';
?>
<main class="container py-5">
    <div class="row g-5">
        <div class="col-lg-5">
            <p class="section-kicker mb-1">Contact</p>
            <h1 class="fw-bold">Ask about destinations, bookings, or trip planning.</h1>
            <p class="text-muted">Messages are stored in the admin panel so staff can review and remove inquiries securely.</p>
            <div class="contact-strip">
                <strong>Support hours</strong>
                <span>Monday to Saturday, 9:00 AM - 6:00 PM</span>
            </div>
        </div>
        <div class="col-lg-7">
            <form class="form-shell needs-validation" method="post" novalidate>
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input class="form-control <?= has_error($errors, 'name') ?>" id="name" name="name" value="<?= old('name') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'name') ?: 'Name is required.' ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control <?= has_error($errors, 'email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'email') ?: 'Valid email is required.' ?></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="subject">Subject</label>
                        <input class="form-control <?= has_error($errors, 'subject') ?>" id="subject" name="subject" value="<?= old('subject') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'subject') ?: 'Subject is required.' ?></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="body">Message</label>
                        <textarea class="form-control <?= has_error($errors, 'body') ?>" id="body" name="body" rows="6" required><?= old('body') ?></textarea>
                        <div class="invalid-feedback"><?= first_error($errors, 'body') ?: 'Message is required.' ?></div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Send message</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

