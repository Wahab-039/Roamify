<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/BudgetController.php';
require_once __DIR__ . '/models/BudgetPlan.php';

require_login();
if (is_post()) {
    BudgetController::delete();
}

$plans = BudgetPlan::forUser((int) current_user()['id']);
$pageTitle = 'Saved Plans';
require __DIR__ . '/views/partials/header.php';
?>
<main class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <p class="section-kicker mb-1">Saved plans</p>
            <h1 class="fw-bold mb-0">Your budget plans</h1>
        </div>
        <a class="btn btn-primary align-self-md-end" href="<?= url('budget.php') ?>">New plan</a>
    </div>
    <div class="row g-4">
        <?php foreach ($plans as $plan): ?>
            <div class="col-md-6 col-xl-4">
                <article class="plan-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1"><?= e($plan['title']) ?></h2>
                            <p class="text-muted small mb-0"><?= e($plan['destination_title'] ?: 'Custom trip') ?></p>
                        </div>
                        <span class="badge text-bg-light"><?= (int) $plan['days'] ?> days</span>
                    </div>
                    <div class="plan-total mt-4">PKR <?= number_format((float) $plan['total_cost']) ?></div>
                    <div class="small text-muted mt-2">
                        Hotel <?= number_format((float) $plan['hotel_cost']) ?> / Food <?= number_format((float) $plan['food_cost']) ?> / Transport <?= number_format((float) $plan['transport_cost']) ?>
                    </div>
                    <form method="post" class="mt-4" onsubmit="return confirm('Delete this plan?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="plan_id" value="<?= (int) $plan['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                    </form>
                </article>
            </div>
        <?php endforeach; ?>
        <?php if (!$plans): ?>
            <div class="col-12"><div class="empty-state">No saved plans yet. Create your first budget plan.</div></div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/views/partials/footer.php'; ?>

