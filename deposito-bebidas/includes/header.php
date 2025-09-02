<?php
session_start();
$pagina = basename($_SERVER['PHP_SELF']);

// Conexão com banco
include 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depósito de Bebidas</title>
    <link rel="stylesheet" href="assets/css/header.css"> <!-- Corrigi caminho -->
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php">🍻 Depósito de Bebidas</a>
        </div>

        <nav>
            <div class="menu-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <ul class="nav-list">
                <li><a href="index.php" class="<?php echo ($pagina == 'index.php') ? 'ativo' : ''; ?>">Início</a></li>
                <li><a href="produtos.php" class="<?php echo ($pagina == 'produtos.php') ? 'ativo' : ''; ?>">Produtos</a></li>
                <li><a href="promocoes.php" class="<?php echo ($pagina == 'promocoes.php') ? 'ativo' : ''; ?>">Promoções</a></li>

                <li class="icone-login">
                    <a href="login.php">
                        <img src="assets/img/icons/login.png" alt="Login" title="Login">
                    </a>
                </li>

                <li class="icone-carrinho">
                    <a href="carrinho.php">
                        <img src="assets/img/icons/carrinho.png" alt="Carrinho" title="Carrinho">
                        <span class="badge" id="carrinho-quantidade">0</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<script>
    // Menu hamburguer
    const menu = document.getElementById('mobile-menu');
    const navList = document.querySelector('.nav-list');
    menu.addEventListener('click', () => {
        navList.classList.toggle('active');
        menu.classList.toggle('toggle');
    });

    // Carrinho quantidade (exemplo)
    let quantidadeCarrinho = 0;
    function atualizarCarrinho(valor) {
        quantidadeCarrinho += valor;
        if(quantidadeCarrinho < 0) quantidadeCarrinho = 0;
        document.getElementById('carrinho-quantidade').textContent = quantidadeCarrinho;
    }
</script>
