<?php

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';
require_once __DIR__ . '/../../Service/Cart/CartService.php';

ensure_session_started();

$page_title = 'Checkout - Finalizar Compra';
$cartService = new CartService($factory);

$validation = $cartService->validateCart();
if (!$validation['success']) {
    header('Location: /Pages/Products/cart.php');
    exit;
}

$cartItems = $cartService->getCartItems();
$cartTotal = $cartService->getCartTotal();
$isLogged = isset($_SESSION['id_usuario']);
$nomeSessao = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$clienteId = isset($_SESSION['cliente_id']) ? (int)$_SESSION['cliente_id'] : null;

// Se logado, tenta buscar os dados para liberação do pedido
$cliente = null;
$enderecoObj = null; // Nova variável para guardar os dados da Rua, Número, etc.
$isEnderecoEntrega = false; 

if ($isLogged && $clienteId) {
    try {
        $cliente = $factory->getClienteDao()->buscaPorId($clienteId);
        
        // Tenta resgatar o endereço oficial se o cliente existir
        if ($cliente) {
            $enderecoId = null;
            if (method_exists($cliente, 'getEnderecoId')) {
                $enderecoId = $cliente->getEnderecoId();
            } elseif (method_exists($cliente, 'getEndereco') && $cliente->getEndereco()) {
                $enderecoId = $cliente->getEndereco()->getId();
            } else {
                $enderecoId = $clienteId; // Fallback
            }
            if ($enderecoId) {
                $enderecoObj = $factory->getEnderecoDao()->buscaPorId($enderecoId);
            }
        }
    } catch (Throwable $e) {
        $cliente = null;
    }

    if (!$cliente) {
        try {
            $enderecoEntrega = $factory->getEnderecoDao()->buscaPorId($clienteId);
            if ($enderecoEntrega) {
                $enderecoObj = $enderecoEntrega; // Guarda o objeto completo
                $cliente = new class($enderecoEntrega) {
                    private $end;
                    public function __construct($end) { $this->end = $end; }
                    public function getId() { return $this->end->getId(); }
                    public function getNome() { return $this->end->getNome(); }
                    public function getTelefone() { return $this->end->getTelefone() ?? '(Não informado)'; }
                    public function getEmail() { return $this->end->getEmail() ?? '(Anônimo)'; }
                    public function getCartaoCredito() { return '0000000000001234'; } 
                };
                $isEnderecoEntrega = true;
            }
        } catch (Throwable $e) {
            $cliente = null;
        }
    }
}

include_once __DIR__ . '/../Common/layout_header.php';
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        <div class="col-md-8">
            <h2><span class="glyphicon glyphicon-ok-sign"></span> Confirmação do Pedido</h2>
            
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Resumo dos Produtos</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th style="text-align: center;">Qtd</th>
                                <th style="text-align: center;">Preço Unit.</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item) {
                                $produto = $item['produto'];
                                $estoque = $item['estoque'];
                                $quantidade = $item['quantidade'];
                                $subtotal = $item['subtotal'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td style="text-align: center;"><?php echo (int)$quantidade; ?></td>
                                    <td style="text-align: center;">R$ <?php echo number_format($estoque ? $estoque->getPreco() : 0, 2, ',', '.'); ?></td>
                                    <td style="text-align: right;">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold; font-size: 16px;">
                                <td colspan="3" style="text-align: right;">TOTAL:</td>
                                <td style="text-align: right;">R$ <?php echo number_format($cartTotal, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <?php if (!$isLogged) { ?>
                <div class="alert alert-warning">
                    <h4><span class="glyphicon glyphicon-warning-sign"></span> Você precisa estar conectado para finalizar a compra</h4>
                    <p>Escolha uma opção abaixo:</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><span class="glyphicon glyphicon-log-in"></span> Cliente Existente</h3>
                            </div>
                            <div class="panel-body">
                                <p>Se você já é nosso cliente, faça login para continuar.</p>
                                <a href="/Pages/Login/index.php?redirect=/Pages/Products/checkout.php" class="btn btn-primary btn-block">
                                    Fazer Login
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Novo Cliente</h3>
                            </div>
                            <div class="panel-body">
                                <p>Se você é novo por aqui, cadastre seus dados de entrega.</p>
                                <a href="/Pages/Addresses/create.php?checkout=1" class="btn btn-success btn-block">
                                    Cadastrar Endereço
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div style="margin-top: 30px;">
                    <a href="/Pages/Products/cart.php" class="btn btn-default">
                        <span class="glyphicon glyphicon-arrow-left"></span> Voltar ao Carrinho
                    </a>
                </div>

            <?php } elseif ($cliente) { ?>
                <div class="alert alert-success">
                    <p><strong>✓ Dados de Entrega Confirmados!</strong></p>
                    <p>Olá, <?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?>. Revise os dados locais de envio abaixo.</p>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><span class="glyphicon glyphicon-map-marker"></span> Destino do Envio</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Comprador:</strong> <?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Recebedor no Local:</strong> <?php echo htmlspecialchars($cliente->getNome(), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Telefone Contato:</strong> <?php echo htmlspecialchars($cliente->getTelefone(), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Pagamento:</strong> Cartão de Crédito (Final <?php echo substr($cliente->getCartaoCredito(), -4); ?>)</p>
                            </div>
                            <div class="col-md-6">
                                <?php if ($enderecoObj) { ?>
                                    <p><strong>Endereço:</strong> <?php echo htmlspecialchars($enderecoObj->getRua() . ', ' . $enderecoObj->getNumero(), ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if(method_exists($enderecoObj, 'getComplemento') && $enderecoObj->getComplemento()) echo ' - ' . htmlspecialchars($enderecoObj->getComplemento(), ENT_QUOTES, 'UTF-8'); ?></p>
                                    
                                    <p><strong>Bairro:</strong> <?php echo method_exists($enderecoObj, 'getBairro') ? htmlspecialchars($enderecoObj->getBairro(), ENT_QUOTES, 'UTF-8') : ''; ?> | <strong>CEP:</strong> <?php echo method_exists($enderecoObj, 'getCep') ? htmlspecialchars($enderecoObj->getCep(), ENT_QUOTES, 'UTF-8') : ''; ?></p>
                                    
                                    <p><strong>Cidade/UF:</strong> <?php echo method_exists($enderecoObj, 'getCidade') ? htmlspecialchars($enderecoObj->getCidade(), ENT_QUOTES, 'UTF-8') : ''; ?><?php echo method_exists($enderecoObj, 'getEstado') ? '/' . htmlspecialchars($enderecoObj->getEstado(), ENT_QUOTES, 'UTF-8') : ''; ?></p>
                                <?php } else { ?>
                                    <p class="text-danger">Detalhes do endereço não encontrados.</p>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <hr style="margin: 10px 0;">
                        
                        <a href="/Pages/Addresses/edit.php?id=<?php echo $enderecoObj ? $enderecoObj->getId() : ''; ?>&checkout=1" class="btn btn-sm btn-info">
                            <span class="glyphicon glyphicon-edit"></span> Editar Meu Endereço
                        </a>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <a href="/Pages/Products/cart.php" class="btn btn-default">
                        <span class="glyphicon glyphicon-arrow-left"></span> Voltar ao Carrinho
                    </a>
                    
                    <form method="POST" action="/Service/Products/checkout_action.php" style="display: inline;">
                        <input type="hidden" name="nome" value="<?php echo htmlspecialchars($nomeSessao, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($cliente->getEmail(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="telefone" value="<?php echo htmlspecialchars($cliente->getTelefone(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="cartao_credito" value="<?php echo htmlspecialchars($cliente->getCartaoCredito(), ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <button type="submit" class="btn btn-success btn-lg">
                            <span class="glyphicon glyphicon-shopping-cart"></span> Confirmar e Gerar Pedido
                        </button>
                    </form>
                </div>

            <?php } else { ?>
                <div class="alert alert-info">
                    <h4><span class="glyphicon glyphicon-map-marker"></span> Endereço de Entrega Pendente</h4>
                    <p>Você precisa registrar para onde enviaremos a mercadoria antes de pagar.</p>
                </div>

                <div style="margin-top: 30px;">
                    <a href="/Pages/Addresses/create.php?checkout=1" class="btn btn-primary btn-lg">
                        <span class="glyphicon glyphicon-plus"></span> Cadastrar Novo Endereço de Entrega
                    </a>
                    <a href="/Pages/Products/cart.php" class="btn btn-default" style="margin-left: 10px;">
                        <span class="glyphicon glyphicon-arrow-left"></span> Voltar ao Carrinho
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Resumo do Pedido</h3>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li>
                            <strong>Produtos:</strong> 
                            <span class="pull-right"><?php echo count($cartItems); ?></span>
                        </li>
                        <li>
                            <strong>Unidades:</strong> 
                            <span class="pull-right"><?php echo array_sum(array_column($cartItems, 'quantidade')); ?></span>
                        </li>
                        <li style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                            <strong>Total:</strong>
                            <span class="pull-right text-success" style="font-size: 18px; font-weight: bold;">
                                R$ <?php echo number_format($cartTotal, 2, ',', '.'); ?>
                            </span>
                        </li>
                    </ul>

                    <?php if ($cliente) { ?>
                        <div class="alert alert-success alert-sm">
                            <small><strong>✓ Tudo pronto!</strong> O botão de finalização está liberado.</small>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning alert-sm">
                            <small><strong>⚠️ Pendência:</strong> Cadastre um endereço de entrega para liberar o pedido.</small>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>