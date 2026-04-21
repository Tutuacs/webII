<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
require_internal_user();

$page_title = 'Novo Fornecedor';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Suppliers/create_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for="endereco_id">Endereço ID</label>
                <input type="number" id="endereco_id" name="endereco_id" class="form-control" min="1">
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/Pages/Suppliers/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
