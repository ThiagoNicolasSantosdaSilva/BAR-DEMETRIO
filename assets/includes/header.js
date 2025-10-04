// ---------------- ABRIR/CERRAR CARRINHO ----------------
const cartIcon = document.querySelector('.cart-icon');
const carrinhoLateral = document.getElementById('carrinho-lateral');
const fecharCarrinho = document.getElementById('fechar-carrinho');
const cartCount = document.getElementById('cart-count');
const itensCarrinho = document.getElementById('itens-carrinho');

// Abre carrinho ao clicar no ícone
cartIcon.addEventListener('click', ()=>{ carrinhoLateral.classList.add('open'); });

// Fecha carrinho ao clicar no X
fecharCarrinho.addEventListener('click', ()=>{ carrinhoLateral.classList.remove('open'); });

// Função para atualizar badge do carrinho dinamicamente
function atualizarCartCount(qtd){
    cartCount.textContent = qtd;
}

// Exemplo de uso: atualizarCartCount(quantidadeTotal);
// O JS do index.js vai controlar os produtos adicionados e chamar essa função
