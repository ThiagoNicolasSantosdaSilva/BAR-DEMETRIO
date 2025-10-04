<?php
session_start();
include "../conexao/conexao.php"; // Conexão com o banco

// Verifica login do cliente
$logado = isset($_SESSION['cliente_id']);
if (!$logado && !isset($_SESSION['temp_cliente_id'])) {
    $_SESSION['temp_cliente_id'] = "0200" . time();
}

// Categorias
$categorias = [
    'geral'=> ['nome'=>'Geral'],
    'promocoes'=> ['nome'=>'Promoções'],
];

// Função para buscar produtos
function buscarProdutos($conn){
    $sql = "SELECT * FROM produtos ORDER BY id ASC";
    $res = mysqli_query($conn, $sql);
    $produtos = [];
    while($row = mysqli_fetch_assoc($res)){
        $produtos[] = $row;
    }
    return $produtos;
}

$produtos = buscarProdutos($conn);

// Quantidade inicial no carrinho
$carrinho_qtd = 0;
if(!empty($_SESSION['carrinho'])){
    foreach($_SESSION['carrinho'] as $item){
        $carrinho_qtd += $item['quantidade'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bar do Demétrio 🍻</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ---------------- RESET ---------------- */
*{margin:0;padding:0;box-sizing:border-box;font-family: 'Segoe UI', Arial, sans-serif;}
body{background:#f9f9f9; color:#222;}

/* ---------------- HEADER ---------------- */
header{background:#222; color:#fff; padding:15px 25px; position:fixed; top:0; left:0; right:0; z-index:1000; box-shadow:0 4px 12px rgba(0,0,0,0.3);}
.header-container{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
.logo a{ color:#fff; font-size:24px; font-weight:bold; text-decoration:none; display:flex; align-items:center; gap:8px; }
.nav-list{ list-style:none; display:flex; gap:25px; align-items:center; flex-wrap:wrap; transition:0.3s; }
.nav-list li a{ color:#fff; text-decoration:none; font-size:16px; transition:0.3s; }
.nav-list li a:hover, .nav-list li a.ativo{ color:#f1c40f; }
.icone-carrinho{ position:relative; cursor:pointer; }
.badge{ position:absolute; top:-8px; right:-12px; background:#ff4d4d; color:#fff; font-size:12px; padding:2px 6px; border-radius:50%; font-weight:bold; }
.menu-toggle{ display:none; cursor:pointer; font-size:24px; color:#fff; transition:0.3s; }
.menu-toggle:hover{ color:#f1c40f; }

/* ---------------- CATEGORIAS E FILTROS ---------------- */
.categorias{ display:flex; gap:15px; justify-content:center; margin-top:120px; flex-wrap:wrap; padding:0 10px; }
.categoria{ background:#f1c40f; padding:12px 20px; border-radius:50px; cursor:pointer; font-weight:bold; color:#222; transition:0.3s; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
.categoria:hover{ background:#d4ac0d; transform:translateY(-2px); }

.filtros{ display:flex; justify-content:center; gap:15px; margin:15px 0; flex-wrap:wrap; }
.filtros select{ padding:7px 12px; border-radius:6px; border:1px solid #ccc; font-size:14px; }

/* ---------------- PRODUTOS HORIZONTAL ---------------- */
.produtos-grid{ display:flex; flex-direction:column; gap:15px; padding:20px; }
.produto-card{
  display:flex;
  align-items:center;
  gap:20px;
  background:#fff;
  padding:15px;
  border-radius:12px;
  border:1px solid #ddd;
  box-shadow:0 4px 15px rgba(0,0,0,0.08);
  transition:0.3s;
}
.produto-card:hover{ transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,0.15); }

.produto-card img{ width:120px; height:120px; object-fit:cover; border-radius:8px; }

.produto-info{ display:flex; align-items:center; gap:20px; flex:1; flex-wrap:wrap; }

/* Nome e preço */
.produto-info .nome{ font-weight:bold; font-size:16px; min-width:150px; }
.produto-info .preco{ font-size:15px; color:#333; min-width:80px; }

/* Radio payment e delivery */
.radio-pagamento, .radio-entrega{
  display:flex; gap:10px; align-items:center;
}
.radio-pagamento label, .radio-entrega label{
  font-size:14px; display:flex; align-items:center; gap:5px;
}

/* Quantidade */
.quantidade{ display:flex; gap:5px; align-items:center; }
.quantidade button{ padding:5px 10px; cursor:pointer; font-weight:bold; background:#f1c40f; border:none; border-radius:6px; transition:0.3s; }
.quantidade button:hover{ background:#d4ac0d; transform:scale(1.1); }
.numero{ font-weight:bold; min-width:25px; text-align:center; }

/* ---------------- CARRINHO LATERAL ---------------- */
#carrinho-lateral{ position:fixed; top:0; right:-400px; width:100%; max-width:400px; height:100%; background:#fff; box-shadow:-3px 0 25px rgba(0,0,0,0.25); transition:0.4s; padding:25px; z-index:1500; overflow-y:auto; }
#carrinho-lateral.open{ right:0; }
#carrinho-lateral .fechar{ position:absolute; top:10px; right:15px; font-size:28px; cursor:pointer; }
.carrinho-item{ display:flex; gap:10px; margin-bottom:18px; align-items:center; border-bottom:1px solid #eee; padding-bottom:10px; }
.carrinho-item img{ width:60px; height:60px; object-fit:cover; border-radius:8px; }
.carrinho-item .info{ flex:1; font-size:14px; }
#finalizar-compra{ margin-top:15px; padding:10px 20px; background:#f1c40f; border:none; border-radius:8px; cursor:pointer; font-weight:bold; font-size:14px; transition:0.3s; width:100%; }
#finalizar-compra:hover{ background:#d4ac0d; }

/* RESPONSIVO */
@media(max-width:768px){
  .produto-info{ flex-direction:column; align-items:flex-start; gap:10px; }
  .menu-toggle{ display:block; } 
  .nav-list{ position:absolute; top:65px; right:0; width:220px; background:#333; flex-direction:column; align-items:flex-start; padding:20px; display:none; border-radius:0 0 10px 10px; } 
  .nav-list.active{ display:flex; } 
}
</style>
</head>
<body>

<header>
<div class="header-container">
  <div class="logo"><a href="index.php"><i class="fa-solid fa-beer-mug-empty"></i> Bar do Demétrio</a></div>
  <nav>
    <div class="menu-toggle" id="mobile-menu"><i class="fa-solid fa-bars"></i></div>
    <ul class="nav-list">
      <li><a href="index.php" class="ativo"><i class="fa-solid fa-house"></i> Início</a></li>
      <li><a href="produtos.php"><i class="fa-solid fa-box"></i> Produtos</a></li>
      <li><a href="promocoes.php"><i class="fa-solid fa-tags"></i> Promoções</a></li>
      <li class="icone-login"><a href="login.php"><i class="fa-solid fa-user"></i></a></li>
      <li class="icone-carrinho"><a href="#" id="abrir-carrinho"><i class="fa-solid fa-cart-shopping"></i>
        <span class="badge" id="carrinho-quantidade"><?php echo $carrinho_qtd;?></span></a></li>
    </ul>
  </nav>
</div>
</header>

<main>
  <!-- Categorias -->
  <div class="categorias">
    <?php foreach($categorias as $key=>$cat): ?>
      <div class="categoria" data-categoria="<?php echo $key;?>"><?php echo $cat['nome'];?></div>
    <?php endforeach; ?>
  </div>

  <!-- Filtro de preço -->
  <div class="filtros">
    <select id="filtro-preco">
      <option value="default">Filtrar por preço</option>
      <option value="menor-maior">Menor para Maior</option>
      <option value="maior-menor">Maior para Menor</option>
    </select>
  </div>

  <!-- Lista de produtos -->
  <div id="produtos-geral" class="produtos-grid">
    <?php foreach($produtos as $p): ?>
      <div class="produto-card" data-categoria="<?php echo $p['categoria'];?>" data-id="<?php echo $p['id'];?>" data-preco="<?php echo $p['preco_pix'];?>">
        <img src="<?php echo $p['imagem'];?>" alt="<?php echo $p['nome'];?>">
        <div class="produto-info">
          <div class="nome"><?php echo $p['nome'];?></div>
          <div class="preco">R$ <?php echo number_format($p['preco_pix'],2,',','.');?></div>

          <!-- Pagamento -->
          <div class="radio-pagamento">
            <label><input type="radio" name="pag<?php echo $p['id'];?>" value="pix" checked> Dinheiro / Pix</label>
            <label><input type="radio" name="pag<?php echo $p['id'];?>" value="cartao"> Cartão</label>
          </div>

          <!-- Entrega -->
          <div class="radio-entrega">
            <label><input type="radio" name="entrega<?php echo $p['id'];?>" value="retirar" checked> Retirar</label>
            <label><input type="radio" name="entrega<?php echo $p['id'];?>" value="entregar"> Entregar</label>
          </div>

          <!-- Quantidade -->
          <div class="quantidade">
            <button class="menos">-</button>
            <span class="numero">0</span>
            <button class="mais">+</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- Carrinho lateral -->
<div id="carrinho-lateral">
  <span class="fechar" id="fechar-carrinho">&times;</span>
  <h2>Carrinho 🛒</h2>
  <div id="itens-carrinho"></div>
  <button id="finalizar-compra">Finalizar Compra</button>
</div>

<script>
// MENU HAMBURGUER
document.getElementById('mobile-menu').addEventListener('click',()=>document.querySelector('.nav-list').classList.toggle('active'));

// CARRINHO
const abrirCarrinho=document.getElementById('abrir-carrinho');
const carrinhoLateral=document.getElementById('carrinho-lateral');
const fecharCarrinho=document.getElementById('fechar-carrinho');
const cartCount=document.getElementById('carrinho-quantidade');
const itensCarrinho=document.getElementById('itens-carrinho');
const btnFinalizar=document.getElementById('finalizar-compra');
let carrinho={};

abrirCarrinho.addEventListener('click', e=>{ e.preventDefault(); carrinhoLateral.classList.add('open'); });
fecharCarrinho.addEventListener('click', ()=>carrinhoLateral.classList.remove('open'));
btnFinalizar.addEventListener('click', ()=>{ sessionStorage.setItem('carrinho',JSON.stringify(carrinho)); window.location.href='carrinho.php'; });

function atualizarBadge(){ let total=0; for(let id in carrinho) total+=carrinho[id].quantidade; cartCount.textContent=total; }
function atualizarItensCarrinho(){ itensCarrinho.innerHTML=''; for(let id in carrinho){ const item=carrinho[id]; const div=document.createElement('div'); div.classList.add('carrinho-item'); div.innerHTML=`<img src="${item.img}" alt="${item.nome}"><div class="info"><strong>${item.nome}</strong><br>Quantidade: ${item.quantidade}<br>Preço: R$ ${item.preco.toFixed(2)}<br>Pagamento: ${item.forma.toUpperCase()}<br>Entrega: ${item.entrega.toUpperCase()}</div>`; itensCarrinho.appendChild(div); }}

// PRODUTOS
document.querySelectorAll('.produto-card').forEach(card=>{
  const mais=card.querySelector('.mais'); 
  const menos=card.querySelector('.menos'); 
  const numero=card.querySelector('.numero'); 
  const nome=card.querySelector('.nome').innerText; 
  const img=card.querySelector('img').src;
  const radios=card.querySelectorAll('input[name^="pag"]'); 
  const radiosEntrega=card.querySelectorAll('input[name^="entrega"]'); 
  let quantidade=0; 
  let preco=parseFloat(card.dataset.preco); 
  let forma='pix'; 
  let entrega='retirar';

  radios.forEach(r=>r.addEventListener('change',()=>{ forma=r.value; preco=(forma==='pix')?parseFloat(card.dataset.preco):parseFloat(card.dataset.preco)*1.1; if(carrinho[card.dataset.id]){ carrinho[card.dataset.id].preco=preco; carrinho[card.dataset.id].forma=forma; atualizarItensCarrinho(); }}));
  radiosEntrega.forEach(r=>r.addEventListener('change',()=>{ entrega=r.value; if(carrinho[card.dataset.id]){ carrinho[card.dataset.id].entrega=entrega; atualizarItensCarrinho(); }}));

  mais.addEventListener('click',()=>{ quantidade++; numero.textContent=quantidade; preco=(forma==='pix')?parseFloat(card.dataset.preco):parseFloat(card.dataset.preco)*1.1; carrinho[card.dataset.id]={nome,img,quantidade,preco,forma,entrega}; atualizarBadge(); atualizarItensCarrinho(); });
  menos.addEventListener('click',()=>{ if(quantidade>0){ quantidade--; numero.textContent=quantidade; if(quantidade===0) delete carrinho[card.dataset.id]; else carrinho[card.dataset.id].quantidade=quantidade; atualizarBadge(); atualizarItensCarrinho(); } });
});

// FILTRO
document.querySelectorAll('.categoria').forEach(cat=>{ cat.addEventListener('click', ()=>{ const selected=cat.dataset.categoria; document.querySelectorAll('.produto-card').forEach(p=>{ p.style.display=(selected==='geral'||p.dataset.categoria===selected)?'flex':'none'; }); }); });
document.getElementById('filtro-preco').addEventListener('change', e=>{ let cards=Array.from(document.querySelectorAll('.produto-card')).filter(c=>c.style.display!=='none'); if(e.target.value==='menor-maior'){ cards.sort((a,b)=>parseFloat(a.dataset.preco)-parseFloat(b.dataset.preco)); } else if(e.target.value==='maior-menor'){ cards.sort((a,b)=>parseFloat(b.dataset.preco)-parseFloat(a.dataset.preco)); } cards.forEach(c=>document.getElementById('produtos-geral').appendChild(c)); });
</script>
</body>
</html>
