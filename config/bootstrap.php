<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

set_exception_handler(function (Throwable $exception): void {
    error_log($exception);
    http_response_code(500);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>Something went wrong</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>';
    echo '<body class="bg-light"><main class="container py-5"><div class="alert alert-danger border-0 shadow-sm">';
    echo '<h1 class="h4">Something went wrong</h1><p class="mb-0">Please check your database setup and try again. Technical details were logged securely.</p>';
    echo '</div></main></body></html>';
    exit;
});

require_once dirname(__DIR__) . '/helpers/functions.php';

