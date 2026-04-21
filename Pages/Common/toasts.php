<?php

function toast(string $message, string $type = 'info'): string
{
    $types = [
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
    ];

    $class = $types[$type] ?? $types['info'];
    return '<div class="alert ' . $class . '" role="alert">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
}
