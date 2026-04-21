<?php

require_once __DIR__ . '/session.php';

ensure_session_started();
$_SESSION = [];
session_destroy();

header('Location: /index.php');
exit;
