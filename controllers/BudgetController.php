<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/validation.php';
require_once dirname(__DIR__) . '/models/BudgetPlan.php';

final class BudgetController
{
    public static function store(): array
    {
        require_login();
        verify_csrf();

        $errors = validate_required($_POST, [
            'title' => 'Plan title',
            'days' => 'Days',
        ]);

        if ((int) ($_POST['days'] ?? 0) < 1) {
            add_error($errors, 'days', 'Days must be at least 1.');
        }

        foreach (['hotel_cost', 'food_cost', 'transport_cost', 'misc_cost'] as $field) {
            if ((float) ($_POST[$field] ?? 0) < 0) {
                add_error($errors, $field, 'Cost cannot be negative.');
            }
        }

        if ($errors) {
            set_old($_POST);
            return $errors;
        }

        BudgetPlan::create((int) current_user()['id'], $_POST);
        clear_old();
        flash('success', 'Budget plan saved.');
        redirect('plans.php');
    }

    public static function delete(): void
    {
        require_login();
        verify_csrf();
        BudgetPlan::deleteForUser((int) ($_POST['plan_id'] ?? 0), (int) current_user()['id']);
        flash('success', 'Budget plan deleted.');
        redirect('plans.php');
    }
}

