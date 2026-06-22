<?php
$isEdit = !empty($destination);
$value = function (string $key, string $default = '') use ($destination): string {
    return old($key, (string) ($destination[$key] ?? $default));
};
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="section-kicker mb-1"><?= $isEdit ? 'Edit' : 'Create' ?></p>
        <h1 class="h3 fw-bold mb-0"><?= $isEdit ? 'Edit destination' : 'Add destination' ?></h1>
    </div>
    <a href="<?= url('admin/destinations.php') ?>" class="btn btn-outline-primary">Back</a>
</div>
<form class="admin-panel needs-validation" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <?php if ($isEdit): ?>
        <input type="hidden" name="destination_id" value="<?= (int) $destination['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="title">Title</label>
            <input class="form-control <?= has_error($errors, 'title') ?>" id="title" name="title" value="<?= $value('title') ?>" required>
            <div class="invalid-feedback"><?= first_error($errors, 'title') ?: 'Title is required.' ?></div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="country">Country</label>
            <input class="form-control <?= has_error($errors, 'country') ?>" id="country" name="country" value="<?= $value('country') ?>" required>
            <div class="invalid-feedback"><?= first_error($errors, 'country') ?: 'Country is required.' ?></div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="city">City</label>
            <input class="form-control <?= has_error($errors, 'city') ?>" id="city" name="city" value="<?= $value('city') ?>" required>
            <div class="invalid-feedback"><?= first_error($errors, 'city') ?: 'City is required.' ?></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="category">Category</label>
            <input class="form-control <?= has_error($errors, 'category') ?>" id="category" name="category" value="<?= $value('category') ?>" placeholder="Beach, Mountain, Heritage" required>
            <div class="invalid-feedback"><?= first_error($errors, 'category') ?: 'Category is required.' ?></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="estimated_cost">Estimated cost PKR</label>
            <input class="form-control <?= has_error($errors, 'estimated_cost') ?>" id="estimated_cost" name="estimated_cost" type="number" min="0" step="0.01" value="<?= $value('estimated_cost', '0') ?>" required>
            <div class="invalid-feedback"><?= first_error($errors, 'estimated_cost') ?: 'Estimated cost is required.' ?></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="best_time">Best time</label>
            <input class="form-control" id="best_time" name="best_time" value="<?= $value('best_time') ?>" placeholder="Oct - Mar">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="location">Location label</label>
            <input class="form-control" id="location" name="location" value="<?= $value('location') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="latitude">Latitude</label>
            <input class="form-control" id="latitude" name="latitude" type="number" step="0.000001" value="<?= $value('latitude') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="longitude">Longitude</label>
            <input class="form-control" id="longitude" name="longitude" type="number" step="0.000001" value="<?= $value('longitude') ?>">
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control <?= has_error($errors, 'description') ?>" id="description" name="description" rows="5" required><?= $value('description') ?></textarea>
            <div class="invalid-feedback"><?= first_error($errors, 'description') ?: 'Description is required.' ?></div>
        </div>
        <div class="col-12">
            <label class="form-label" for="travel_tips">Travel tips</label>
            <textarea class="form-control" id="travel_tips" name="travel_tips" rows="4"><?= $value('travel_tips') ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="image">Primary image</label>
            <input class="form-control <?= has_error($errors, 'image') ?>" id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <div class="form-text">JPG, PNG, or WEBP up to 2MB.</div>
            <div class="invalid-feedback d-block"><?= first_error($errors, 'image') ?></div>
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-3">
                <input class="form-check-input" id="featured" name="featured" type="checkbox" value="1" <?= checked((bool) old('featured', (string) ($destination['featured'] ?? '0'))) ?>>
                <label class="form-check-label" for="featured">Featured destination</label>
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Update destination' : 'Create destination' ?></button>
        </div>
    </div>
</form>

<?php if ($isEdit && $images): ?>
    <div class="admin-panel mt-4">
        <h2 class="h5 fw-bold mb-3">Images</h2>
        <div class="row g-3">
            <?php foreach ($images as $image): ?>
                <div class="col-sm-6 col-lg-3">
                    <img class="admin-gallery-img" src="<?= e(destination_image($image['image_path'])) ?>" alt="">
                    <form method="post" class="mt-2" onsubmit="return confirm('Remove this image?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_image">
                        <input type="hidden" name="image_id" value="<?= (int) $image['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger w-100" type="submit">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

