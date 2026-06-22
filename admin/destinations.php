<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/controllers/AdminDestinationController.php';
require_once dirname(__DIR__) . '/models/Destination.php';

$action = (string) ($_GET['action'] ?? 'list');
$id = (int) ($_GET['id'] ?? 0);
$errors = [];

if (is_post()) {
    $postedAction = (string) ($_POST['action'] ?? 'save');
    if ($postedAction === 'delete') {
        AdminDestinationController::delete();
    } elseif ($postedAction === 'delete_image') {
        AdminDestinationController::deleteImage();
    } else {
        $errors = AdminDestinationController::save(!empty($_POST['destination_id']) ? (int) $_POST['destination_id'] : null);
    }
}

$editing = $action === 'edit' && $id > 0;
$creating = $action === 'create';
$destination = $editing ? Destination::find($id) : null;
$images = $destination ? Destination::images((int) $destination['id']) : [];
$pageTitle = $creating ? 'Add Destination' : ($editing ? 'Edit Destination' : 'Manage Destinations');

if ($creating || $editing) {
    require dirname(__DIR__) . '/views/partials/admin_header.php';
    require dirname(__DIR__) . '/views/admin_destination_form.php';
    require dirname(__DIR__) . '/views/partials/admin_footer.php';
    exit;
}

$destinations = Destination::search([], 1, 200);
require dirname(__DIR__) . '/views/partials/admin_header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <p class="section-kicker mb-1">Catalog</p>
        <h1 class="h3 fw-bold mb-0">Manage destinations</h1>
    </div>
    <a href="<?= url('admin/destinations.php?action=create') ?>" class="btn btn-primary align-self-md-end">Add destination</a>
</div>
<div class="admin-panel">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Destination</th><th>Category</th><th>Cost</th><th>Featured</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                <?php foreach ($destinations as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img class="admin-thumb" src="<?= e(destination_image($item['primary_image'] ?? null)) ?>" alt="<?= e($item['title']) ?>">
                                <div><strong><?= e($item['title']) ?></strong><div class="small text-muted"><?= e($item['city']) ?>, <?= e($item['country']) ?></div></div>
                            </div>
                        </td>
                        <td><?= e($item['category']) ?></td>
                        <td>PKR <?= number_format((float) $item['estimated_cost']) ?></td>
                        <td><?= !empty($item['featured']) ? 'Yes' : 'No' ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/destinations.php?action=edit&id=' . (int) $item['id']) ?>">Edit</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this destination?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="destination_id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$destinations): ?>
                    <tr><td colspan="5" class="text-muted">No destinations found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require dirname(__DIR__) . '/views/partials/admin_footer.php'; ?>

