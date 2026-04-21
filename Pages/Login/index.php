<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
ensure_session_started();

if (isset($_SESSION['id_usuario'])) {
    header('Location: /index.php');
    exit;
}

$redirect = isset($_GET['redirect']) ? (string) $_GET['redirect'] : '/index.php';
if ($redirect === '' || $redirect[0] !== '/') {
    $redirect = '/index.php';
}

$page_title = 'DAO3 Shop - Login';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-5 col-md-offset-3">
        <form action="/Service/Auth/login_action.php" method="POST" role="form" class="panel panel-default" style="padding: 20px;">
            <legend>Acesse sua conta</legend>
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" class="form-control" id="login" name="login" placeholder="Informe o login" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Informe a senha" required>
            </div>

            <p class="text-muted">Clientes acessam catálogo e pedidos. Internos acessam gestão.</p>

            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php';
