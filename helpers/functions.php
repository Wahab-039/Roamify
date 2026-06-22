<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/session.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    if ($base === '') {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if (str_ends_with($scriptDir, '/admin')) {
            $scriptDir = substr($scriptDir, 0, -6);
        }
        $base = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    }

    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function upload_url(string $filename): string
{
    if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
        return $filename;
    }

    return url('uploads/destinations/' . ltrim($filename, '/'));
}

function redirect(string $path): never
{
    if (preg_match('#^https?://#i', $path)) {
        $targetHost = parse_url($path, PHP_URL_HOST);
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        $target = $targetHost === $currentHost ? $path : url('index.php');
    } else {
        $target = url($path);
    }

    header('Location: ' . $target);
    exit;
}

function is_post(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        flash('danger', 'Your session expired. Please try again.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flash(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $messages;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function selected(string $actual, string $expected): string
{
    return $actual === $expected ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}

function first_error(array $errors, string $field): string
{
    return e($errors[$field][0] ?? '');
}

function has_error(array $errors, string $field): string
{
    return isset($errors[$field]) ? 'is-invalid' : '';
}

function destination_image(?string $image): string
{
    return $image ? upload_url($image) : asset('img/destination-placeholder.svg');
}

function clean_float(mixed $value): float
{
    return max(0, (float) $value);
}

function clean_int(mixed $value): int
{
    return max(0, (int) $value);
}

function save_uploaded_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('Image upload failed or exceeds 2MB.');
    }

    $tmpName = $file['tmp_name'] ?? '';
    $originalName = $file['name'] ?? 'image';
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpName);
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($mime, $allowedMimes, true)) {
        throw new RuntimeException('Invalid image type.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
    $destination = UPLOAD_DIR . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($tmpName, $destination)) {
        throw new RuntimeException('Unable to save uploaded image.');
    }

    return $filename;
}
