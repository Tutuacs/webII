<?php

function render_toast(string $message, string $type = 'info'): string
{
    $types = [
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
    ];

    $class = isset($types[$type]) ? $types[$type] : $types['info'];
    return '<div class="alert ' . $class . '" role="alert">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
}

function render_flash_toast(): string
{
    if (!function_exists('pull_flash_message')) {
        return '';
    }

    $flash = pull_flash_message();
    if (!$flash || empty($flash['message'])) {
        return '';
    }

    $type = isset($flash['type']) ? (string) $flash['type'] : 'info';
    $message = (string) $flash['message'];

    return render_toast($message, $type);
}
