<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

require_guest();
$errors = [];
if (is_post()) {
    $errors = AuthController::register();
}

$pageTitle = 'Create Account';
require __DIR__ . '/views/partials/header.php';
?>
<main class="auth-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form class="form-shell needs-validation" method="post" novalidate>
                    <?= csrf_field() ?>
                    <p class="section-kicker mb-1">Join Roamify</p>
                    <h1 class="h3 fw-bold mb-4">Create your travel account</h1>
                    <div class="mb-3">
                        <label class="form-label" for="name">Name</label>
                        <input class="form-control <?= has_error($errors, 'name') ?>" id="name" name="name" value="<?= old('name') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'name') ?: 'Name is required.' ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control <?= has_error($errors, 'email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'email') ?: 'Valid email is required.' ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control <?= has_error($errors, 'password') ?>" id="password" name="password" type="password" minlength="8" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'password') ?: 'Use at least 8 characters.' ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <input class="form-control <?= has_error($errors, 'password_confirmation') ?>" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'password_confirmation') ?: 'Confirm your password.' ?></div>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Create account</button>
                    <p class="text-center text-muted mt-3 mb-0">Already registered? <a href="<?= url('login.php') ?>">Log in</a></p>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

