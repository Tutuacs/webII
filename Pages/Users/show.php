<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$usuario = $factory->getUsuarioDao()->buscaPorId($id);

if (!$usuario) {
    header('Location: /Pages/Users/list.php');
    exit;
}

$page_title = 'Detalhes do Usuário';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section>
    <h2><?php echo htmlspecialchars($usuario->getNome(), ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><strong>ID:</strong> <?php echo (int) $usuario->getId(); ?></p>
    <p><strong>Login:</strong> <?php echo htmlspecialchars($usuario->getLogin(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Perfil:</strong> <?php echo htmlspecialchars($usuario->getRole(), ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="/Pages/Users/list.php" class="btn btn-default">Voltar</a>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
