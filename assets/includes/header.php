<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina = basename($_SERVER['PHP_SELF']);

// Conexão com banco
include __DIR__ . '/../../conexao/conexao.php';

// Conta quantos itens já existem no carrinho (sessão temporária ou logada)
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ---------------- RESET ---------------- */
* { margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif; }

/* ---------------- HEADER ---------------- */
header {
  background: #222;
  color: #fff;
  padding: 10px 25px;
  position: fixed;
  top:0; left:0; right:0;
  z-index:1000;
  box-shadow:0 2px 8px rgba(0,0,0,0.3);
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* LOGO */
.logo a {
  color: #fff;
  font-size: 22px;
  font-weight: bold;
  text-decoration: none;
}

/* MENU */
.nav-list {
  list-style: none;
  display: flex;
  align-items: center;
  gap: 25px;
}

.nav-list li a {
  color: #fff;
  text-decoration: none;
  font-size: 16px;
  transition: 0.3s;
}

.nav-list li a:hover,
.nav-list li a.ativo { color: #f1c40f; }

/* ÍCONES */
.icone-carrinho {
  position: relative;
}

.badge {
  position: absolute;
  top:-8px;
  right:-12px;
  background: #ff4d4d;
  color:#fff;
  font-size:12px;
  padding:2px 6px;
  border-radius:50%;
  font-weight:bold;
}

/* MENU HAMBURGUER */
.menu-toggle { display:none; cursor:pointer; font-size:22px; color:#fff; }

/* ---------------- CARRINHO LATERAL ---------------- */
#carrinho-lateral {
  position: fixed;
  top:0;
  right:-400px; /* escondido */
  width:400px;
  height:100%;
  background:#fff;
  box-shadow:-3px 0 15px rgba(0,0,0,0.2);
  transition:0.3s;
  padding:20px;
  z-index:1001;
  overflow-y:auto;
}

#carrinho-lateral.open { right:0; }

#carrinho-lateral .fechar {
  position:absolute;
  top:10px;
  right:10px;
  font-size:26px;
  cursor:pointer;
}

#carrinho-lateral h2 { margin-bottom:20px; }

/* ITENS DO CARRINHO */
.carrinho-item {
  display:flex;
  gap:10px;
  margin-bottom:15px;
  align-items:center;
  border-bottom:1px solid #eee;
  padding-bottom:10px;
}

.carrinho-item img { width:50px; height:50px; object-fit:cover; border-radius:4px; }

.carrinho-item .info { flex:1; font-size:14px; }

/* ---------------- RESPONSIVO ---------------- */
@media(max-width:768px){
  .menu-toggle { display:block; }
  .nav-list {
    position:absolute;
    top:70px;
    right:0;
    width:200px;
    background:#333;
    flex-direction:column;
    align-items:flex-start;
    padding:20px;
    display:none;
  }
  .nav-list.active { display:flex; }
}
</style>
</head>

<body>

<header>
  <div class="header-container">
    <!-- LOGO -->
    <div class="logo">
      <a href="../index/index.php">
        <i class="fa-solid fa-beer-mug-empty"></i> Depósito de Bebidas
      </a>
    </div>

    <!-- MENU -->
    <nav>
      <div class="menu-toggle" id="mobile-menu">
        <i class="fa-solid fa-bars"></i>
      </div>

      <ul class="nav-list">
        <li><a href="../index/index.php" class="<?php echo ($pagina == 'index.php') ? 'ativo' : ''; ?>">
          <i class="fa-solid fa-house"></i> Início</a></li>
        <li><a href="../produtos/produtos.php" class="<?php echo ($pagina == 'produtos.php') ? 'ativo' : ''; ?>">
          <i class="fa-solid fa-box"></i> Produtos</a></li>
        <li><a href="../promocoes/promocoes.php" class="<?php echo ($pagina == 'promocoes.php') ? 'ativo' : ''; ?>">
          <i class="fa-solid fa-tags"></i> Promoções</a></li>
        <!-- ÍCONES -->
        <li class="icone-login">
          <a href="../login/login.php" title="Login">
            <i class="fa-solid fa-user"></i>
          </a>
        </li>
        <li class="icone-carrinho">
          <a href="#" id="abrir-carrinho" title="Carrinho">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="badge" id="carrinho-quantidade"><?php echo $carrinho_qtd; ?></span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</header>

<!-- CARRINHO LATERAL -->
<div id="carrinho-lateral">
  <span class="fechar" id="fechar-carrinho">&times;</span>
  <h2>Carrinho 🛒</h2>
  <div id="itens-carrinho"></div>
</div>

<script>
/* ---------------- MENU HAMBURGUER ---------------- */
const menu = document.getElementById('mobile-menu');
const navList = document.querySelector('.nav-list');
if(menu){
  menu.addEventListener('click', ()=>{
    navList.classList.toggle('active');
    menu.classList.toggle('toggle');
  });
}

/* ---------------- CARRINHO LATERAL ---------------- */
const abrirCarrinho = document.getElementById('abrir-carrinho');
const carrinhoLateral = document.getElementById('carrinho-lateral');
const fecharCarrinho = document.getElementById('fechar-carrinho');
const cartCount = document.getElementById('carrinho-quantidade');
const itensCarrinho = document.getElementById('itens-carrinho');

// Abrir carrinho
abrirCarrinho.addEventListener('click',(e)=>{
  e.preventDefault();
  carrinhoLateral.classList.add('open');
});

// Fechar carrinho
fecharCarrinho.addEventListener('click',()=>{
  carrinhoLateral.classList.remove('open');
});

// ---------------- FUNÇÃO ATUALIZAR BADGE ----------------
function atualizarCartCount(qtd){
  cartCount.textContent = qtd;
}

// ---------------- FUNÇÃO ATUALIZAR ITENS CARRINHO ----------------
function atualizarItensCarrinho(carrinho){
  itensCarrinho.innerHTML='';
  for(let id in carrinho){
    const item = carrinho[id];
    const div = document.createElement('div');
    div.classList.add('carrinho-item');
    div.innerHTML=`
      <!-- Imagem opcional -->
      <!-- <img src="../assets/img/produtos/${id}.png" alt="${item.nome}"> -->
      <div class="info">
        <strong>${item.nome}</strong><br>
        Quantidade: ${item.quantidade}<br>
        Preço: R$ ${item.preco.toFixed(2)}
      </div>
    `;
    itensCarrinho.appendChild(div);
  }
}

// Exemplo: quando atualizar o carrinho no index.js, chame:
// atualizarCartCount(totalQuantidade);
// atualizarItensCarrinho(carrinhoObj);
</script>

</body>
</html>
