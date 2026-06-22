<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/auth.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Message.php';

final class AdminController
{
    public static function updateUserStatus(): void
    {
        require_admin();
        verify_csrf();
        $status = (string) ($_POST['status'] ?? 'active');
        if (!in_array($status, ['active', 'blocked'], true)) {
            $status = 'active';
        }
        User::updateStatus((int) ($_POST['user_id'] ?? 0), $status);
        flash('success', 'User status updated.');
        redirect('admin/users.php');
    }

    public static function deleteUser(): void
    {
        require_admin();
        verify_csrf();
        User::delete((int) ($_POST['user_id'] ?? 0));
        flash('success', 'User deleted.');
        redirect('admin/users.php');
    }

    public static function deleteMessage(): void
    {
        require_admin();
        verify_csrf();
        Message::delete((int) ($_POST['message_id'] ?? 0));
        flash('success', 'Message deleted.');
        redirect('admin/messages.php');
    }
}

