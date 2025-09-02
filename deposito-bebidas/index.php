<?php 
session_start();
include 'includes/conexao.php'; 
$logado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Depósito de Bebidas 🍻</title>
  <link rel="stylesheet" href="assets/css/index.css">
  <style>
    /* RESET */
    * {margin:0; padding:0; box-sizing: border-box;}

    body {font-family: Arial, sans-serif; background:#fafafa; color:#222;}

    /* HEADER FIXO */
    header {
      position: fixed; top:0; left:0; right:0;
      background:#222; color:#fff;
      display:flex; justify-content:space-between; align-items:center;
      padding:10px 20px; z-index:1000;
    }
    header .logo {font-weight:bold; font-size:20px;}
    header nav a {
      color:#fff; text-decoration:none; margin:0 10px;
    }
    header nav a:hover {text-decoration:underline;}
    header .icons {display:flex; align-items:center; gap:15px;}
    #carrinho-quantidade {
      background:red; color:#fff; border-radius:50%;
      padding:2px 6px; font-size:12px;
      position:relative; top:-8px; left:-10px;
    }

    main {margin-top:80px; padding:20px;}

    h1 {margin-bottom:20px; text-align:center;}

    /* CARROSSEL PRINCIPAL */
    .carrossel-container {overflow:hidden; position:relative; margin-bottom:30px;}
    .carrossel-slides {display:flex; transition:transform .5s ease-in-out;}
    .carrossel-slides img {width:100%; flex-shrink:0; max-height:300px; object-fit:cover;}
    .carrossel-indicators {text-align:center; margin-top:10px;}
    .carrossel-indicators .indicator {
      display:inline-block; width:12px; height:12px; margin:0 5px;
      border-radius:50%; background:#ccc; cursor:pointer;
    }
    .carrossel-indicators .ativo {background:#222;}

    /* CATEGORIAS */
    .carrossel-circulos {margin:40px 0;}
    .circulos-container {
      display:flex; overflow-x:auto; gap:20px; padding-bottom:10px;
    }
    .circulo {
      flex:0 0 auto; text-align:center; cursor:pointer;
    }
    .circulo img {width:60px; height:60px; border-radius:50%; object-fit:cover;}
    .circulo span {display:block; margin-top:5px; font-size:14px;}
    .circulo.ativo img {border:3px solid #222;}

    .conteudo-circulos .conteudo {display:none;}
    .conteudo-circulos .conteudo.ativo {display:block;}

    /* PRODUTOS */
    ul.lista-produtos {
      list-style:none; padding:0;
      display:grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr));
      gap:20px; margin-top:20px;
    }
    ul.lista-produtos li {
      background:#fff; padding:15px; border-radius:10px;
      box-shadow:0 2px 6px rgba(0,0,0,.1);
      display:flex; flex-direction:column; align-items:center;
    }
    ul.lista-produtos .descricao {font-weight:bold; margin-bottom:10px; text-align:center;}
    .quantidade {display:flex; align-items:center; gap:10px;}
    .quantidade button {
      background:#222; color:#fff; border:none;
      padding:5px 10px; border-radius:5px; cursor:pointer;
    }

    /* BOTÃO FINALIZAR */
    #finalizar-compra {
      display:block; margin:40px auto 0;
      padding:12px 25px; background:green; color:#fff;
      border:none; border-radius:8px; font-size:16px; cursor:pointer;
    }

    /* RESPONSIVO */
    @media(max-width:600px){
      header nav {display:none;}
      h1 {font-size:20px;}
      .circulo img {width:50px; height:50px;}
    }
  </style>
</head>
<body>

<header>
  <div class="logo">Depósito 🍺</div>
  <nav>
    <a href="index.php">Início</a>
    <a href="promocoes.php">Promoções</a>
    <a href="contato.php">Contato</a>
  </nav>
  <div class="icons">
    <?php if($logado): ?>
      <i>👤</i>
    <?php else: ?>
      <i onclick="abrirCadastro()">🔑</i>
    <?php endif; ?>
    <div style="position:relative;">
      <i>🛒</i><span id="carrinho-quantidade">0</span>
    </div>
  </div>
</header>

<main>
  <h1>Bem-vindo ao Depósito de Bebidas 🍻</h1>

  <!-- Carrossel -->
  <div class="carrossel-container">
    <div class="carrossel-slides">
      <?php
      $imagens = ['1.png','2.png','3.png','4.png','5.png','6.png'];
      foreach($imagens as $img){
          echo "<img src='assets/img/$img' alt='Bebida'>";
      }
      ?>
    </div>
    <div class="carrossel-indicators">
      <?php foreach($imagens as $i=>$img){
        $ativo = $i===0?'ativo':'';
        echo "<span class='indicator $ativo' data-index='$i'></span>";
      }?>
    </div>
  </div>

  <!-- Categorias + Produtos -->
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

    <div class="conteudo-circulos">
      <?php
      $resCategoriasProdutos=mysqli_query($conn,"SELECT * FROM categorias ORDER BY id ASC");
      $index=0;
      while($categoria=mysqli_fetch_assoc($resCategoriasProdutos)){
        $ativo=$index===0?'ativo':'';
        echo "<div class='conteudo $ativo' data-index='$index'>";
        $cat_id=$categoria['id'];
        $produtos=mysqli_query($conn,"SELECT * FROM produtos WHERE categoria_id=$cat_id ORDER BY id ASC");
        if(mysqli_num_rows($produtos)>0){
          echo "<ul class='lista-produtos'>";
          while($produto=mysqli_fetch_assoc($produtos)){
            echo "<li data-id='{$produto['id']}' data-estoque='{$produto['estoque']}'>
                    <span class='descricao'>{$produto['nome']} (Estoque: {$produto['estoque']})</span>
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
</main>

<script>
function abrirCadastro(){
  alert("Você precisa se cadastrar ou logar para continuar!");
}

/* Carrossel */
let slides=document.querySelectorAll('.carrossel-slides img');
let indicators=document.querySelectorAll('.carrossel-indicators .indicator');
let currentIndex=0, intervalo=3000;

function mostrarSlide(index){
  document.querySelector('.carrossel-slides').style.transform=`translateX(-${index*100}%)`;
  indicators.forEach((ind,i)=>ind.classList.toggle('ativo',i===index));
  currentIndex=index;
}
function proximoSlide(){currentIndex=(currentIndex+1)%slides.length; mostrarSlide(currentIndex);}
mostrarSlide(0); let slideInterval=setInterval(proximoSlide,intervalo);
indicators.forEach((ind,i)=>ind.addEventListener('click',()=>{mostrarSlide(i); clearInterval(slideInterval); slideInterval=setInterval(proximoSlide,intervalo);}))

/* Categorias */
const circulos=document.querySelectorAll('.circulo');
const conteudos=document.querySelectorAll('.conteudo');
circulos.forEach(c=>c.addEventListener('click',()=>{
  let i=c.dataset.index;
  circulos.forEach(x=>x.classList.remove('ativo'));
  conteudos.forEach(x=>x.classList.remove('ativo'));
  c.classList.add('ativo');
  document.querySelector(`.conteudo[data-index="${i}"]`).classList.add('ativo');
}));

/* Carrinho */
let quantidadeCarrinho=0;
document.querySelectorAll('.lista-produtos li').forEach(li=>{
  const btnMais=li.querySelector('.mais');
  const btnMenos=li.querySelector('.menos');
  const numero=li.querySelector('.numero');
  const estoqueInicial=parseInt(li.dataset.estoque);
  let estoqueAtual=estoqueInicial;

  btnMais.addEventListener('click',()=>{
    if(estoqueAtual>0){
      numero.textContent=parseInt(numero.textContent)+1;
      quantidadeCarrinho++;
      estoqueAtual--;
      document.getElementById('carrinho-quantidade').textContent=quantidadeCarrinho;
    }else{alert("Estoque esgotado!");}
  });
  btnMenos.addEventListener('click',()=>{
    let valor=parseInt(numero.textContent);
    if(valor>0){
      numero.textContent=valor-1;
      quantidadeCarrinho--;
      estoqueAtual++;
      document.getElementById('carrinho-quantidade').textContent=quantidadeCarrinho;
    }
  });
});

/* Finalizar */
document.getElementById('finalizar-compra').addEventListener('click',()=>{
  let itens=[];
  document.querySelectorAll('.lista-produtos li').forEach(li=>{
    let qtd=parseInt(li.querySelector('.numero').textContent);
    if(qtd>0){
      itens.push({id:li.dataset.id, quantidade:qtd});
    }
  });
  if(itens.length===0){alert("Carrinho vazio!"); return;}
  fetch('finalizar.php',{
    method:'POST', headers:{'Content-Type':'application/json'},
    body:JSON.stringify({itens})
  })
  .then(res=>res.json())
  .then(data=>{
    if(data.sucesso){alert(data.mensagem); location.reload();}
    else{alert("Erro: "+data.mensagem);}
  });
});
</script>

</body>
</html>
