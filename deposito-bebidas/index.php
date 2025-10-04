<?php 
session_start();
include 'includes/conexao.php';
include 'includes/header.php';
$logado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Depósito de Bebidas 🍻</title>
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<main>
  <h1>Bem-vindo ao Depósito de Bebidas 🍻</h1>

  <!-- CARROSSEL -->
  <div class="carrossel-container">
    <div class="carrossel-slides">
      <a href="#cervejas"><img src="assets/img/1.png" alt="Promoções"></a>
      <a href="#destilados"><img src="assets/img/2.png" alt="Destilados"></a>
      <a href="#refrigerantes"><img src="assets/img/3.png" alt="Refrigerantes"></a>
    </div>
    <div class="carrossel-indicators">
      <span class="indicator ativo" data-index="0"></span>
      <span class="indicator" data-index="1"></span>
      <span class="indicator" data-index="2"></span>
    </div>
  </div>

  <!-- CATEGORIAS -->
  <section class="carrossel-circulos">
    <div class="circulos-container">
      <?php
      $resCategorias = mysqli_query($conn,"SELECT * FROM categorias ORDER BY id ASC");
      $i=0;
      while($categoria=mysqli_fetch_assoc($resCategorias)){
        $ativo=$i===0?'ativo':'';
        $icone=$categoria['icone'] ?: 'default.png';
        $nome=$categoria['nome'] ?: 'Categoria';
        echo "<div class='circulo $ativo' data-index='$i'>
                <img src='assets/img/icons/$icone' alt='$nome'>
                <span>$nome</span>
              </div>";
        $i++;
      }
      ?>
    </div>

    <!-- PRODUTOS -->
    <div class="conteudo-circulos">
      <?php
      $resCategoriasProdutos=mysqli_query($conn,"SELECT * FROM categorias ORDER BY id ASC");
      $index=0;
      while($categoria=mysqli_fetch_assoc($resCategoriasProdutos)){
        $ativo=$index===0?'ativo':'';
        $idHTML = strtolower(preg_replace('/[^a-zA-Z0-9]/','',$categoria['nome']));
        echo "<div class='conteudo $ativo' data-index='$index' id='$idHTML'>";
        $cat_id=(int)$categoria['id'];
        $produtos=mysqli_query($conn,"SELECT * FROM produtos WHERE categoria_id=$cat_id ORDER BY id ASC");
        if(mysqli_num_rows($produtos)>0){
          echo "<ul class='lista-produtos'>";
          while($produto=mysqli_fetch_assoc($produtos)){
            $id      = $produto['id'];
            $nome    = htmlspecialchars($produto['nome']);
            $estoque = (int)$produto['estoque'];
            $preco   = number_format($produto['preco'], 2, ',', '.'); // Formato BR

            echo "<li data-id='$id' data-estoque='$estoque' data-preco='{$produto['preco']}'>
                    <span class='descricao'>$nome - R$ $preco (Estoque: $estoque)</span>
                    <div class='quantidade'>
                      <button class='menos'>-</button>
                      <span class='numero'>0</span>
                      <button class='mais'>+</button>
                    </div>
                  </li>";
          }
          echo "</ul>";
        } else {
          echo "<p>Nenhum produto cadastrado.</p>";
        }
        echo "</div>";
        $index++;
      }
      ?>
    </div>
  </section>
  
  <button id="finalizar-compra">Finalizar Compra</button>

  <!-- MODAL RESUMO -->
  <div id="resumo-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Resumo do Carrinho 🛒</h2>
      <ul id="lista-resumo"></ul>
      <h3 id="total-geral">Total: R$ 0,00</h3>
      <button id="confirmar-compra">Confirmar Compra</button>
    </div>
  </div>
</main>

<script src="assets/js/index.js"></script>
</body>
</html>
