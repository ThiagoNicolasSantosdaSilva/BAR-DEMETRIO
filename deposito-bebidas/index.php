<?php 
include 'includes/conexao.php'; // Primeiro a conexão
include 'includes/header.php'; 
?>

<link rel="stylesheet" href="assets/css/index.css">

<h1>Bem-vindo ao Depósito de Bebidas 🍻</h1>

<!-- Carrossel principal de imagens -->
<div class="carrossel-container">
    <div class="carrossel-slides">
        <?php
        $imagens = ['1.png','2.png','3.png','4.png','5.png','6.png','7.png','8.png'];
        foreach($imagens as $img){
            echo "<img src='assets/img/$img' alt='Bebida'>";
        }
        ?>
    </div>
    <div class="carrossel-indicators">
        <?php foreach($imagens as $i => $img){
            $ativo = $i===0 ? 'ativo' : '';
            echo "<span class='indicator $ativo' data-index='$i'></span>";
        } ?>
    </div>
</div>

<!-- Carrossel de categorias -->
<section class="carrossel-circulos">
    <div class="circulos-container">
        <?php
        $resCategorias = mysqli_query($conn, "SELECT * FROM categorias ORDER BY id ASC");
        $i = 0;
        while($categoria = mysqli_fetch_assoc($resCategorias)){
            $ativo = $i===0 ? 'ativo' : '';
            $icone = !empty($categoria['icone']) ? $categoria['icone'] : 'default.png';
            $nome = !empty($categoria['nome']) ? $categoria['nome'] : 'Categoria';
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
        // Resetar o ponteiro ou fazer nova consulta
        $resCategoriasProdutos = mysqli_query($conn, "SELECT * FROM categorias ORDER BY id ASC");
        $index = 0;
        while($categoria = mysqli_fetch_assoc($resCategoriasProdutos)){
            $ativo = $index===0 ? 'ativo' : '';
            echo "<div class='conteudo $ativo' data-index='$index'>";
            $cat_id = $categoria['id'];
            $produtos = mysqli_query($conn, "SELECT * FROM produtos WHERE categoria_id=$cat_id ORDER BY id ASC");

            if(mysqli_num_rows($produtos) > 0){
                echo "<ul class='lista-produtos'>";
                while($produto = mysqli_fetch_assoc($produtos)){
                    echo "<li>
                            <span class='descricao'>{$produto['nome']}</span>
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

<script>
window.addEventListener('DOMContentLoaded', () => {
    /* Carrossel de imagens principal */
    let slides = document.querySelectorAll('.carrossel-slides img');
    let indicators = document.querySelectorAll('.carrossel-indicators .indicator');
    let currentIndex = 0;
    let intervalo = 3000;

    function mostrarSlide(index) {
        const container = document.querySelector('.carrossel-slides');
        container.style.transform = `translateX(-${index * 100}%)`;
        indicators.forEach((ind, i) => ind.classList.toggle('ativo', i === index));
        currentIndex = index;
    }

    function proximoSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        mostrarSlide(currentIndex);
    }

    mostrarSlide(0);
    let slideInterval = setInterval(proximoSlide, intervalo);

    indicators.forEach((indicator, i) => {
        indicator.addEventListener('click', () => {
            mostrarSlide(i);
            clearInterval(slideInterval);
            slideInterval = setInterval(proximoSlide, intervalo);
        });
    });

    /* Carrossel de categorias */
    const circulos = document.querySelectorAll('.circulo');
    const conteudos = document.querySelectorAll('.conteudo');

    circulos.forEach(circulo => {
        circulo.addEventListener('click', () => {
            const index = circulo.dataset.index;
            circulos.forEach(c => c.classList.remove('ativo'));
            conteudos.forEach(c => c.classList.remove('ativo'));
            circulo.classList.add('ativo');
            document.querySelector(`.conteudo[data-index="${index}"]`).classList.add('ativo');
        });
    });

    /* Lista de produtos + / - e atualização carrinho */
    let quantidadeCarrinho = 0;
    document.querySelectorAll('.quantidade').forEach(div => {
        const btnMais = div.querySelector('.mais');
        const btnMenos = div.querySelector('.menos');
        const numero = div.querySelector('.numero');

        btnMais.addEventListener('click', () => {
            numero.textContent = parseInt(numero.textContent) + 1;
            quantidadeCarrinho++;
            document.getElementById('carrinho-quantidade').textContent = quantidadeCarrinho;
        });

        btnMenos.addEventListener('click', () => {
            let valor = parseInt(numero.textContent);
            if(valor > 0){
                numero.textContent = valor - 1;
                quantidadeCarrinho--;
                document.getElementById('carrinho-quantidade').textContent = quantidadeCarrinho;
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
