<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
require_internal_user();

$page_title = 'Novo Usuário';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Users/create_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="role">Perfil</label>
                <select id="role" name="role" class="form-control">
                    <option value="INTERNO">Interno</option>
                    <option value="CLIENTE">Cliente</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/Pages/Users/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
