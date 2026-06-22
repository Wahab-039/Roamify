<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';

if (current_admin()) {
    redirect('admin/index.php');
}

$errors = [];
if (is_post()) {
    $errors = AuthController::adminLogin();
}

$pageTitle = 'Admin Login';
require dirname(__DIR__) . '/views/partials/header.php';
?>
<main class="auth-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <form class="form-shell needs-validation" method="post" novalidate>
                    <?= csrf_field() ?>
                    <p class="section-kicker mb-1">Admin panel</p>
                    <h1 class="h3 fw-bold mb-4">Secure admin login</h1>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control <?= has_error($errors, 'email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'email') ?: 'Email is required.' ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control <?= has_error($errors, 'password') ?>" id="password" name="password" type="password" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'password') ?: 'Password is required.' ?></div>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Log in</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require dirname(__DIR__) . '/views/partials/footer.php'; ?>

