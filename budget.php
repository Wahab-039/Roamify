<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/BudgetController.php';
require_once __DIR__ . '/models/Destination.php';

require_login();
$errors = [];
if (is_post()) {
    $errors = BudgetController::store();
}

$destinations = Destination::search([], 1, 200);
$selectedDestinationId = (string) ($_GET['destination_id'] ?? old('destination_id'));
$pageTitle = 'Budget Planner';
require __DIR__ . '/views/partials/header.php';
?>
<main class="container py-5">
    <div class="row g-5">
        <div class="col-lg-5">
            <p class="section-kicker mb-1">Budget planner</p>
            <h1 class="fw-bold">Estimate your total trip cost.</h1>
            <p class="text-muted">Enter daily costs and trip days. The calculator updates instantly and the saved plan remains available in your account.</p>
            <div class="total-preview">
                <span>Total estimate</span>
                <strong id="budgetTotal">PKR 0</strong>
            </div>
        </div>
        <div class="col-lg-7">
            <form class="form-shell needs-validation" method="post" id="budgetForm" novalidate>
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label" for="title">Plan title</label>
                        <input class="form-control <?= has_error($errors, 'title') ?>" id="title" name="title" value="<?= old('title', 'My travel plan') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'title') ?: 'Plan title is required.' ?></div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="destination_id">Destination</label>
                        <select class="form-select" id="destination_id" name="destination_id">
                            <option value="">Custom trip</option>
                            <?php foreach ($destinations as $item): ?>
                                <option value="<?= (int) $item['id'] ?>" <?= selected($selectedDestinationId, (string) $item['id']) ?>><?= e($item['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="days">Days</label>
                        <input class="form-control budget-input <?= has_error($errors, 'days') ?>" id="days" name="days" type="number" min="1" value="<?= old('days', '3') ?>" required>
                        <div class="invalid-feedback"><?= first_error($errors, 'days') ?: 'Days must be at least 1.' ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="hotel_cost">Hotel / day</label>
                        <input class="form-control budget-input <?= has_error($errors, 'hotel_cost') ?>" id="hotel_cost" name="hotel_cost" type="number" min="0" step="0.01" value="<?= old('hotel_cost', '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="food_cost">Food / day</label>
                        <input class="form-control budget-input <?= has_error($errors, 'food_cost') ?>" id="food_cost" name="food_cost" type="number" min="0" step="0.01" value="<?= old('food_cost', '0') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="transport_cost">Transport / day</label>
                        <input class="form-control budget-input <?= has_error($errors, 'transport_cost') ?>" id="transport_cost" name="transport_cost" type="number" min="0" step="0.01" value="<?= old('transport_cost', '0') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="misc_cost">Misc / day</label>
                        <input class="form-control budget-input <?= has_error($errors, 'misc_cost') ?>" id="misc_cost" name="misc_cost" type="number" min="0" step="0.01" value="<?= old('misc_cost', '0') ?>">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Save plan</button>
                        <a class="btn btn-outline-primary ms-2" href="<?= url('plans.php') ?>">View plans</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

