<?php
header('Location: /index.php' . (isset($_GET['q']) && trim((string) $_GET['q']) !== '' ? '?q=' . urlencode(trim((string) $_GET['q'])) : ''));
exit;
