<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina = basename($_SERVER['PHP_SELF']);

// Conexão com banco
include __DIR__ . '/conexao.php';

// Conta quantos itens já existem no carrinho (se usar $_SESSION['carrinho'])
$carrinho_qtd = 0;
if (!empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $carrinho_qtd += $item['quantidade'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depósito de Bebidas</title>
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
    <div class="header-container">
        <!-- LOGO -->
        <div class="logo">
            <a href="index.php">
                <i class="fa-solid fa-beer-mug-empty"></i> Depósito de Bebidas
            </a>
        </div>

        <!-- MENU -->
        <nav>
            <div class="menu-toggle" id="mobile-menu">
                <i class="fa-solid fa-bars"></i>
            </div>

            <ul class="nav-list">
                <li><a href="index.php" class="<?php echo ($pagina == 'index.php') ? 'ativo' : ''; ?>">
                    <i class="fa-solid fa-house"></i> Início</a></li>
                <li><a href="produtos.php" class="<?php echo ($pagina == 'produtos.php') ? 'ativo' : ''; ?>">
                    <i class="fa-solid fa-box"></i> Produtos</a></li>
                <li><a href="promocoes.php" class="<?php echo ($pagina == 'promocoes.php') ? 'ativo' : ''; ?>">
                    <i class="fa-solid fa-tags"></i> Promoções</a></li>
                
                <!-- ÍCONES -->
                <li class="icone-login">
                    <a href="login.php" title="Login">
                        <i class="fa-solid fa-user"></i>
                    </a>
                </li>
                <li class="icone-carrinho">
                    <a href="carrinho.php" title="Carrinho">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="badge" id="carrinho-quantidade"><?php echo $carrinho_qtd; ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<script src="assets/js/header.js"></script>
</body>
</html>
