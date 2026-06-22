<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/helpers/validation.php';
require_once dirname(__DIR__) . '/models/Admin.php';

final class AuthController
{
    public static function register(): array
    {
        verify_csrf();
        $errors = validate_required($_POST, [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
        ]);

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirmation'] ?? '');

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            add_error($errors, 'email', 'Enter a valid email address.');
        }

        if (strlen($password) < 8) {
            add_error($errors, 'password', 'Password must be at least 8 characters.');
        }

        if ($password !== $confirm) {
            add_error($errors, 'password_confirmation', 'Password confirmation does not match.');
        }

        if ($email !== '' && User::findByEmail($email)) {
            add_error($errors, 'email', 'This email is already registered.');
        }

        if ($errors) {
            set_old($_POST);
            return $errors;
        }

        $userId = User::create($name, $email, $password);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        clear_old();
        flash('success', 'Welcome to Roamify, your account is ready.');
        redirect('index.php');
    }

    public static function login(): array
    {
        verify_csrf();
        $errors = validate_required($_POST, [
            'email' => 'Email',
            'password' => 'Password',
        ]);

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = $errors ? null : User::authenticate($email, $password);

        if (!$user) {
            add_error($errors, 'email', 'Invalid email or password.');
        }

        if ($errors) {
            set_old(['email' => $email]);
            return $errors;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        clear_old();
        flash('success', 'You are logged in.');
        redirect('index.php');
    }

    public static function adminLogin(): array
    {
        verify_csrf();
        $errors = validate_required($_POST, [
            'email' => 'Email',
            'password' => 'Password',
        ]);

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $admin = $errors ? null : Admin::authenticate($email, $password);

        if (!$admin) {
            add_error($errors, 'email', 'Invalid admin credentials.');
        }

        if ($errors) {
            set_old(['email' => $email]);
            return $errors;
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        clear_old();
        flash('success', 'Admin login successful.');
        redirect('admin/index.php');
    }

    public static function logout(string $redirectTo = 'index.php'): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect($redirectTo);
    }
}

