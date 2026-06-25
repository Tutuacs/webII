<?php

require_once __DIR__ . '/../../Service/Auth/session.php';
require_internal_user();

// 1. Puxa o nome do usuário logado da sessão
$nomeSessao = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// 2. Verifica se estamos no fluxo de checkout para arrumar o botão Cancelar
$isCheckout = isset($_GET['checkout']) || isset($_POST['checkout']);
$cancelarLink = $isCheckout ? '/Pages/Products/checkout.php' : '/Pages/Addresses/list.php';

$page_title = 'Novo Cadastro de Cliente e Entrega';
include_once __DIR__ . '/../Common/layout_header.php';
?>
<section class="row">
    <div class="col-md-8 col-md-offset-2">
        <form action="/Service/Addresses/create_action.php" method="post" class="panel panel-default" style="padding:20px;">
            
            <?php if ($isCheckout): ?>
                <input type="hidden" name="checkout" value="1">
            <?php endif; ?>

            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px;">Dados Pessoais (Cliente)</h4>
            
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" class="form-control" 
                       value="<?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?>" 
                       <?php echo $nomeSessao ? 'readonly' : 'required'; ?>>
                <?php if ($nomeSessao): ?>
                    <small class="text-muted">Dados automáticos do login</small>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="cartao_credito">Cartão de Crédito (16 dígitos)</label>
                <input type="text" id="cartao_credito" name="cartao_credito" class="form-control" maxlength="16" required>
            </div>

            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 30px;">Endereço de Entrega</h4>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" class="form-control" required>
                </div>
                <div class="col-md-8 form-group">
                    <label for="rua">Rua</label>
                    <input type="text" id="rua" name="rua" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="numero">Número</label>
                    <input type="text" id="numero" name="numero" class="form-control" required>
                </div>
                <div class="col-md-8 form-group">
                    <label for="complemento">Complemento (Opcional)</label>
                    <input type="text" id="complemento" name="complemento" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-5 form-group">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" required>
                </div>
                <div class="col-md-5 form-group">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" required>
                </div>
                <div class="col-md-2 form-group">
                    <label for="estado">UF</label>
                    <input type="text" id="estado" name="estado" class="form-control" maxlength="2" required>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-floppy-disk"></span> Guardar
                </button>
                <a href="<?php echo $cancelarLink; ?>" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</section>
<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>