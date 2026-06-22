<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

require_guest();
$errors = [];
if (is_post()) {
    $errors = AuthController::login();
}

$pageTitle = 'Login';
require __DIR__ . '/views/partials/header.php';
?>
<main class="auth-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <form class="form-shell needs-validation" method="post" novalidate>
                    <?= csrf_field() ?>
                    <p class="section-kicker mb-1">Welcome back</p>
                    <h1 class="h3 fw-bold mb-4">Log in to continue planning</h1>
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
                    <p class="text-center text-muted mt-3 mb-0">New here? <a href="<?= url('register.php') ?>">Create an account</a></p>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

