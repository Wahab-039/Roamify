<?php
declare(strict_types=1);

function validate_required(array $data, array $fields): array
{
    $errors = [];

    foreach ($fields as $field => $label) {
        if (trim((string) ($data[$field] ?? '')) === '') {
            $errors[$field][] = $label . ' is required.';
        }
    }

    return $errors;
}

function add_error(array &$errors, string $field, string $message): void
{
    $errors[$field][] = $message;
}

