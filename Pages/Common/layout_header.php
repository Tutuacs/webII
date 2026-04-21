<?php
require_once __DIR__ . '/../../Service/Auth/session.php';
ensure_session_started();

require_once __DIR__ . '/Components/toast.php';

include_once __DIR__ . '/Components/header.php';
include_once __DIR__ . '/Components/navbar.php';

$layoutClass = 'container app-content';
if (isset($layout_variant) && $layout_variant === 'products') {
    $layoutClass .= ' app-content-products';
}
?>
<main class="<?php echo htmlspecialchars($layoutClass, ENT_QUOTES, 'UTF-8'); ?>">
	<?php echo render_flash_toast(); ?>
