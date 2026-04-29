<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
require_internal_user();

$page_title = 'Novo Estoque';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8">
        <form action="/Service/Stock/create_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço</label>
                <input type="text" id="preco" name="preco" class="form-control" placeholder="0,00" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/Pages/Stock/list.php" class="btn btn-default">Cancelar</a>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
