<?php
require_once __DIR__ . '/../../../Service/Auth/session.php';
require_once __DIR__ . '/../../../Service/Cart/CartService.php';

ensure_session_started();

$usuarioLogado = isset($_SESSION['nome_usuario']);
$roleUsuario = $usuarioLogado && isset($_SESSION['role_usuario']) ? $_SESSION['role_usuario'] : null;
$currentUri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/index.php';
$loginHref = '/Pages/Login/index.php?redirect=' . urlencode($currentUri);

$searchContext = 'Produtos';
$searchAction = '/index.php';
$searchParam = 'q';
$searchValue = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$showSearch = (strpos($currentUri, '/index.php') === 0 || $currentUri === '/');

$navbarCartService = new CartService($factory);
$itensNoCarrinho = $navbarCartService->getCartItemCount(); 
?>
<nav class="navbar navbar-default ecom-navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand ecom-brand" href="/index.php">DAO3 Shop</a>
        </div>

        <ul class="nav navbar-nav">
            <li><a href="/index.php">Produtos</a></li>
            <?php if ($usuarioLogado && $roleUsuario === 'INTERNO') { ?>
                <li><a href="/Pages/Products/list.php">Gerenciar Produtos</a></li>
                <li><a href="/Pages/Stock/list.php">Estoque</a></li>
                <li><a href="/Pages/Users/list.php">Usuários</a></li>
                <li><a href="/Pages/Suppliers/list.php">Fornecedores</a></li>
                <li><a href="/Pages/Addresses/list.php">Endereços</a></li>
            <?php } ?>
        </ul>

        <?php if ($showSearch) { ?>
            <form class="navbar-form navbar-left" action="<?php echo htmlspecialchars($searchAction, ENT_QUOTES, 'UTF-8'); ?>" method="get" role="search">
                <div class="input-group ecom-search-group">
                    <input type="text" class="form-control" name="<?php echo htmlspecialchars($searchParam, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Pesquisar <?php echo htmlspecialchars($searchContext, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">Buscar</button>
                    </span>
                </div>
            </form>
        <?php } ?>

        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="/Pages/Products/cart.php">
                    <span class="glyphicon glyphicon-shopping-cart"></span> Carrinho
                    <?php if ($itensNoCarrinho > 0) { ?>
                        <span class="label label-success" style="margin-left: 5px; font-size: 11px;">
                            <?php echo $itensNoCarrinho; ?>
                        </span>
                    <?php } ?>
                </a>
            </li>

            <?php if ($usuarioLogado) {
                $perfil = $roleUsuario ? ' (' . $roleUsuario . ')' : '';
            ?>
                <li class="navbar-text">Olá, <?php echo htmlspecialchars($_SESSION['nome_usuario'] . $perfil, ENT_QUOTES, 'UTF-8'); ?></li>
                <li><a href="/Service/Auth/logout_action.php">Sair</a></li>
            <?php } else { ?>
                <li><a href="<?php echo htmlspecialchars($loginHref, ENT_QUOTES, 'UTF-8'); ?>">Entrar</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>