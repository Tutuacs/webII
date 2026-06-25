<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

require_internal_user();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$endereco = $factory->getEnderecoDao()->buscaPorId($id);

if (!$endereco) {
    header('Location: /Pages/Addresses/list.php');
    exit;
}

$page_title = 'Editar Endereço';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8 col-md-offset-2">
        <form action="/Service/Addresses/update_action.php" method="post" class="panel panel-default" style="padding:20px;">
            <input type="hidden" name="id" value="<?php echo $endereco->getId(); ?>">
            
            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px;">Endereço Físico</h4>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" class="form-control" value="<?php echo htmlspecialchars($endereco->getCep(), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="col-md-8 form-group">
                    <label for="rua">Rua</label>
                    <input type="text" id="rua" name="rua" class="form-control" value="<?php echo htmlspecialchars($endereco->getRua(), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="numero">Número</label>
                    <input type="text" id="numero" name="numero" class="form-control" value="<?php echo htmlspecialchars($endereco->getNumero(), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="col-md-8 form-group">
                    <label for="complemento">Complemento (Opcional)</label>
                    <input type="text" id="complemento" name="complemento" class="form-control" value="<?php echo htmlspecialchars($endereco->getComplemento(), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-5 form-group">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" value="<?php echo htmlspecialchars($endereco->getBairro(), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="col-md-5 form-group">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" value="<?php echo htmlspecialchars($endereco->getCidade(), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="col-md-2 form-group">
                    <label for="estado">UF</label>
                    <input type="text" id="estado" name="estado" class="form-control" maxlength="2" value="<?php echo htmlspecialchars($endereco->getEstado(), ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-refresh"></span> Atualizar
                </button>
                <a href="/Pages/Addresses/list.php" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>