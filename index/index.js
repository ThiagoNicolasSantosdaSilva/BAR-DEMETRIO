// ---------------- POP-UP ----------------
const popup = document.getElementById('popup-cliente');
const fecharPopup = document.getElementById('fechar-popup');
const continuarSemCadastro = document.getElementById('continuar-sem-cadastro');

fecharPopup.addEventListener('click', ()=> { popup.style.display = 'none'; });
continuarSemCadastro.addEventListener('click', e=>{
    e.preventDefault();
    popup.style.display = 'none';
});

// ---------------- CARRINHO LATERAL ----------------
const carrinhoIcon = document.querySelector('.cart-icon');
const carrinhoLateral = document.getElementById('carrinho-lateral');
const fecharCarrinho = document.getElementById('fechar-carrinho');
const itensCarrinho = document.getElementById('itens-carrinho');
let carrinho = {};
let quantidadeTotal = 0;

carrinhoIcon.addEventListener('click', ()=> { carrinhoLateral.classList.add('open'); });
fecharCarrinho.addEventListener('click', ()=> { carrinhoLateral.classList.remove('open'); });

// ---------------- PRODUTOS ----------------
document.querySelectorAll('.produto-card').forEach(card => {
    const mais = card.querySelector('.mais');
    const menos = card.querySelector('.menos');
    const numero = card.querySelector('.numero');
    const precoBase = parseFloat(card.querySelector('.preco').dataset.preco);
    const selectPagamento = card.querySelector('.forma-pagamento');
    const id = card.dataset.id;
    const nome = card.querySelector('.nome').textContent;

    function calcularPreco(){ 
        return selectPagamento.value==='pix'? (precoBase*0.9).toFixed(2) : precoBase.toFixed(2); 
    }

    function atualizarCarrinho(){
        itensCarrinho.innerHTML='';
        quantidadeTotal=0;
        for(let key in carrinho){
            quantidadeTotal += carrinho[key].quantidade;
            const item = carrinho[key];
            const div = document.createElement('div');
            div.classList.add('carrinho-item');
            div.innerHTML = `
                <!--<img src="${item.img}" alt="${item.nome}">-->
                <div class="info">
                    <div>${item.nome}</div>
                    <div>R$ ${item.preco}</div>
                </div>
                <div class="quantidade">
                    <button class="menos">-</button>
                    <span class="numero">${item.quantidade}</span>
                    <button class="mais">+</button>
                </div>
            `;
            div.querySelector('.mais').addEventListener('click', ()=>{
                item.quantidade++;
                item.preco = calcularPreco();
                atualizarCarrinho();
                numero.textContent = item.quantidade;
            });
            div.querySelector('.menos').addEventListener('click', ()=>{
                item.quantidade--;
                if(item.quantidade<=0){ delete carrinho[key]; numero.textContent=0; }
                else numero.textContent = item.quantidade;
                atualizarCarrinho();
            });
            itensCarrinho.appendChild(div);
        }
        document.getElementById('cart-count').textContent = quantidadeTotal;
    }

    mais.addEventListener('click', ()=>{
        if(!carrinho[id]) carrinho[id] = {nome, quantidade:0, preco:calcularPreco()};
        carrinho[id].quantidade++;
        carrinho[id].preco = calcularPreco();
        numero.textContent = carrinho[id].quantidade;
        atualizarCarrinho();
    });

    menos.addEventListener('click', ()=>{
        if(carrinho[id] && carrinho[id].quantidade>0){
            carrinho[id].quantidade--;
            if(carrinho[id].quantidade<=0){ delete carrinho[id]; numero.textContent=0; }
            else numero.textContent = carrinho[id].quantidade;
            atualizarCarrinho();
        }
    });

    selectPagamento.addEventListener('change', ()=>{
        if(carrinho[id]) carrinho[id].preco = calcularPreco();
        card.querySelector('.preco').textContent = 'R$ '+calcularPreco();
        atualizarCarrinho();
    });
});
