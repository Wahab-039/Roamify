<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/validation.php';
require_once dirname(__DIR__) . '/models/Destination.php';

final class AdminDestinationController
{
    public static function save(?int $id = null): array
    {
        require_admin();
        verify_csrf();

        $errors = validate_required($_POST, [
            'title' => 'Title',
            'country' => 'Country',
            'city' => 'City',
            'category' => 'Category',
            'description' => 'Description',
            'estimated_cost' => 'Estimated cost',
        ]);

        if ((float) ($_POST['estimated_cost'] ?? 0) < 0) {
            add_error($errors, 'estimated_cost', 'Estimated cost cannot be negative.');
        }

        if ($errors) {
            set_old($_POST);
            return $errors;
        }

        try {
            if ($id) {
                Destination::update($id, $_POST);
                $destinationId = $id;
                $message = 'Destination updated.';
            } else {
                $destinationId = Destination::create($_POST);
                $message = 'Destination created.';
            }

            $uploaded = save_uploaded_image($_FILES['image'] ?? []);
            if ($uploaded) {
                Destination::addImage($destinationId, $uploaded, true);
            }
        } catch (RuntimeException $exception) {
            add_error($errors, 'image', $exception->getMessage());
            set_old($_POST);
            return $errors;
        }

        clear_old();
        flash('success', $message);
        redirect('admin/destinations.php');
    }

    public static function delete(): void
    {
        require_admin();
        verify_csrf();
        Destination::delete((int) ($_POST['destination_id'] ?? 0));
        flash('success', 'Destination deleted.');
        redirect('admin/destinations.php');
    }

    public static function deleteImage(): void
    {
        require_admin();
        verify_csrf();
        Destination::deleteImage((int) ($_POST['image_id'] ?? 0));
        flash('success', 'Image removed.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'admin/destinations.php');
    }
}

