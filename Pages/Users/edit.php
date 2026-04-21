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

$page_title = 'Editar Usuário';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Users/update_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <input type="hidden" name="id" value="<?php echo (int) $usuario->getId(); ?>">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login" class="form-control" value="<?php echo htmlspecialchars($usuario->getLogin(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario->getNome(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Perfil</label>
                <select id="role" name="role" class="form-control">
                    <option value="INTERNO" <?php echo $usuario->getRole() === 'INTERNO' ? 'selected' : ''; ?>>Interno</option>
                    <option value="CLIENTE" <?php echo $usuario->getRole() === 'CLIENTE' ? 'selected' : ''; ?>>Cliente</option>
                </select>
            </div>
            <div class="form-group">
                <label for="senha">Nova senha (opcional)</label>
                <input type="password" id="senha" name="senha" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="/Pages/Users/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
