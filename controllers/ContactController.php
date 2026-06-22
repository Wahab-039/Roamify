<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/validation.php';
require_once dirname(__DIR__) . '/models/Message.php';

final class ContactController
{
    public static function store(): array
    {
        verify_csrf();
        $errors = validate_required($_POST, [
            'name' => 'Name',
            'email' => 'Email',
            'subject' => 'Subject',
            'body' => 'Message',
        ]);

        $email = trim((string) ($_POST['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            add_error($errors, 'email', 'Enter a valid email address.');
        }

        if (strlen(trim((string) ($_POST['body'] ?? ''))) < 10) {
            add_error($errors, 'body', 'Message must be at least 10 characters.');
        }

        if ($errors) {
            set_old($_POST);
            return $errors;
        }

        Message::create($_POST);
        clear_old();
        flash('success', 'Thanks. Your message has been sent.');
        redirect('contact.php');
    }
}

