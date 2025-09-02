<?php
$pagina = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depósito de Bebidas</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
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
                <li><a href="index.php" class="<?= $pagina == 'index.php' ? 'ativo' : '' ?>">Início</a></li>
                <li><a href="produtos.php" class="<?= $pagina == 'produtos.php' ? 'ativo' : '' ?>">Produtos</a></li>
                <li><a href="carrinho.php" class="<?= $pagina == 'carrinho.php' ? 'ativo' : '' ?>">Carrinho</a></li>
                <li><a href="contato.php" class="<?= $pagina == 'contato.php' ? 'ativo' : '' ?>">Contato</a></li>
            </ul>
        </nav>
    </header>
    <main>
    <script>
        const menu = document.getElementById('mobile-menu');
        const navList = document.querySelector('.nav-list');

        menu.addEventListener('click', () => {
            navList.classList.toggle('active');
            menu.classList.toggle('toggle');
        });
    </script>
