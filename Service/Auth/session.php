<?php

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function set_flash_message(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function pull_flash_message(): ?array
{
    ensure_session_started();

    if (!isset($_SESSION['flash_message']) || !is_array($_SESSION['flash_message'])) {
        return null;
    }

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

    return $flash;
}

function require_login(): void
{
    ensure_session_started();

    if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nome_usuario'])) {
        header('Location: /Pages/Login/index.php');
        exit;
    }
}

function require_internal_user(): void
{
    require_login();

    if (!isset($_SESSION['role_usuario']) || $_SESSION['role_usuario'] !== 'INTERNO') {
        header('Location: /index.php');
        exit;
    }
}

function safe_remove_with_flash(callable $removeCallback, string $redirectPath, string $entityLabel = 'item'): void
{
    try {
        $removeCallback();
        set_flash_message('success', ucfirst($entityLabel) . ' excluído com sucesso.');
    } catch (PDOException $e) {
        if ((string) $e->getCode() === '23000') {
            set_flash_message('danger', 'Não é possível excluir este ' . $entityLabel . ' porque ele está relacionado a outros registros.');
        } else {
            set_flash_message('danger', 'Não foi possível excluir este ' . $entityLabel . '.');
        }
    } catch (Throwable $e) {
        set_flash_message('danger', 'Não foi possível excluir este ' . $entityLabel . '.');
    }

    header('Location: ' . $redirectPath);
    exit;
}
