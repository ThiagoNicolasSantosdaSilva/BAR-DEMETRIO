/* ---------------- Carrossel ---------------- */
const slides = document.querySelectorAll('.carrossel-slides a');
const indicators = document.querySelectorAll('.carrossel-indicators .indicator');
let currentIndex = 0, intervalo = 3000;

function mostrarSlide(index){
  document.querySelector('.carrossel-slides').style.transform = `translateX(-${index*100}%)`;
  indicators.forEach((ind,i) => ind.classList.toggle('ativo', i===index));
  currentIndex = index;
}

function proximoSlide(){
  currentIndex = (currentIndex + 1) % slides.length;
  mostrarSlide(currentIndex);
}

mostrarSlide(0);
let slideInterval = setInterval(proximoSlide, intervalo);

indicators.forEach((ind, i) =>
  ind.addEventListener('click', ()=>{
    mostrarSlide(i);
    clearInterval(slideInterval);
    slideInterval = setInterval(proximoSlide, intervalo);
  })
);

/* ---------------- Categorias ---------------- */
const circulos = document.querySelectorAll('.circulo');
const conteudos = document.querySelectorAll('.conteudo');

circulos.forEach(c =>
  c.addEventListener('click', ()=>{
    let i = c.dataset.index;
    circulos.forEach(x => x.classList.remove('ativo'));
    conteudos.forEach(x => x.classList.remove('ativo'));
    c.classList.add('ativo');
    document.querySelector(`.conteudo[data-index="${i}"]`).classList.add('ativo');
  })
);

/* ---------------- Carrinho ---------------- */
let quantidadeCarrinho = 0;
let carrinho = {}; // {id: {nome, quantidade, estoque}}

document.querySelectorAll('.lista-produtos li').forEach(li=>{
  const btnMais = li.querySelector('.mais');
  const btnMenos = li.querySelector('.menos');
  const numero = li.querySelector('.numero');
  const estoqueInicial = parseInt(li.dataset.estoque);
  const id = li.dataset.id;
  const nome = li.querySelector('.descricao').textContent;
  let estoqueAtual = estoqueInicial;

  btnMais.addEventListener('click', ()=>{
    if(estoqueAtual > 0){
      numero.textContent = parseInt(numero.textContent) + 1;
      quantidadeCarrinho++;
      estoqueAtual--;
      if(!carrinho[id]) carrinho[id] = {nome, quantidade:0, estoque:estoqueInicial};
      carrinho[id].quantidade++;
      document.getElementById('carrinho-quantidade').textContent = quantidadeCarrinho;
    } else { alert("Estoque esgotado!"); }
  });

  btnMenos.addEventListener('click', ()=>{
    let valor = parseInt(numero.textContent);
    if(valor > 0){
      numero.textContent = valor - 1;
      quantidadeCarrinho--;
      estoqueAtual++;
      carrinho[id].quantidade--;
      if(carrinho[id].quantidade <= 0) delete carrinho[id];
      document.getElementById('carrinho-quantidade').textContent = quantidadeCarrinho;
    }
  });
});

/* ---------------- Modal Resumo ---------------- */
const modal = document.getElementById('resumo-modal');
const listaResumo = document.getElementById('lista-resumo');
const finalizarBtn = document.getElementById('finalizar-compra');
const fecharModal = document.querySelector('.modal .close');

function atualizarResumo(){
  listaResumo.innerHTML = '';
  for(let id in carrinho){
    let item = carrinho[id];
    let li = document.createElement('li');
    li.innerHTML = `
      <div class="info">${item.nome}</div>
      <div class="acoes">
        <button class="menos">-</button>
        <span class="qtd">${item.quantidade}</span>
        <button class="mais">+</button>
      </div>
    `;
    li.querySelector('.mais').addEventListener('click', ()=>{
      document.querySelector(`li[data-id="${id}"] .mais`).click();
      atualizarResumo();
    });
    li.querySelector('.menos').addEventListener('click', ()=>{
      document.querySelector(`li[data-id="${id}"] .menos`).click();
      atualizarResumo();
    });
    listaResumo.appendChild(li);
  }
}

finalizarBtn.addEventListener('click', ()=>{
  if(quantidadeCarrinho === 0){ alert("Carrinho vazio!"); return; }
  atualizarResumo();
  modal.style.display = 'flex';
});

fecharModal.addEventListener('click', ()=> modal.style.display = 'none');
window.addEventListener('click', e => { if(e.target===modal) modal.style.display='none'; });

document.getElementById('confirmar-compra').addEventListener('click', ()=>{
  alert("Compra confirmada! (Aqui você pode salvar no banco)");
  modal.style.display='none';
  location.reload();
});
