<?php
session_start();
include "conexao.php"; // Conexão com o banco

// Função para buscar todas as tabelas
function buscarTabelas($conn){
    $res = mysqli_query($conn, "SHOW TABLES");
    $tabelas = [];
    while($row = mysqli_fetch_row($res)){
        $tabelas[] = $row[0];
    }
    return $tabelas;
}

// Função para buscar registros de uma tabela
function buscarRegistros($conn, $tabela){
    $res = mysqli_query($conn, "SELECT * FROM `$tabela`");
    $registros = [];
    while($row = mysqli_fetch_assoc($res)){
        $registros[] = $row;
    }
    return $registros;
}

// Função para buscar campos da tabela
function buscarCampos($conn, $tabela){
    $res = mysqli_query($conn, "DESCRIBE `$tabela`");
    $campos = [];
    while($row = mysqli_fetch_assoc($res)){
        $campos[] = $row['Field'];
    }
    return $campos;
}

$tabelas = buscarTabelas($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel de Controle - Bar do Demétrio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body{font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px;}
h1{color:#222;}
.tabela-container{background:#fff; padding:15px; border-radius:8px; margin-bottom:30px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.tabela-container table{width:100%; border-collapse: collapse;}
.tabela-container th, .tabela-container td{padding:8px; border:1px solid #ddd; text-align:left;}
.tabela-container th{background:#f1c40f; color:#222;}
.botao{padding:7px 12px; margin:2px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; transition:0.3s;}
.botao:hover{opacity:0.8;}
.adicionar{background:#27ae60; color:#fff;}
.editar{background:#2980b9; color:#fff;}
.remover{background:#c0392b; color:#fff;}
.adicionar-campo{background:#8e44ad; color:#fff;}
.remover-campo{background:#e67e22; color:#fff;}
.nova-tabela{background:#16a085; color:#fff; padding:10px 15px; margin-bottom:20px; display:inline-block;}
#formNovaTabela{background:#fff; padding:15px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-bottom:30px;}
#formNovaTabela input, #formNovaTabela select{padding:7px; margin:5px 0; width:100%; border-radius:5px; border:1px solid #ccc;}
#addCampoContainer input, #addCampoContainer select{padding:5px; margin-right:5px;}
#addCampoContainer button{margin-top:5px;}
.badge{display:inline-block; padding:2px 6px; border-radius:4px; color:#fff; font-size:12px; margin-right:2px;}
.badge-nome{background:#3498db;}
.badge-categoria{background:#9b59b6;}
.badge-imagem{background:#e74c3c;}
.badge-preco{background:#f1c40f; color:#222;}
.badge-cartao{background:#2ecc71;}
.badge-estoque{background:#e67e22;}
</style>
</head>
<body>
<h1>Painel de Controle - Bar do Demétrio</h1>

<!-- Formulário para criar nova tabela -->
<button class="nova-tabela" onclick="document.getElementById('formNovaTabela').style.display='block';">Adicionar Nova Tabela</button>

<div id="formNovaTabela" style="display:none;">
<form method="post" action="acoes.php">
    <h3>Adicionar Nova Tabela</h3>
    <input type="text" name="nome_tabela" placeholder="Nome da tabela" required>
    <div id="camposContainer">
        <div>
            <input type="text" name="campos[]" placeholder="Nome do campo" required>
            <select name="tipos[]">
                <option value="nome">Nome</option>
                <option value="categoria">Categoria</option>
                <option value="imagem">Imagem</option>
                <option value="preco">Preço Dinheiro/Pix</option>
                <option value="cartao">Cartão</option>
                <option value="estoque">Estoque</option>
            </select>
        </div>
    </div>
    <button type="button" onclick="adicionarCampo()">Adicionar Outro Campo</button>
    <button type="submit" name="acao" value="criar_tabela">Finalizar Criação</button>
</form>
</div>

<script>
function adicionarCampo(){
    let container = document.getElementById('camposContainer');
    let div = document.createElement('div');
    div.innerHTML = `<input type="text" name="campos[]" placeholder="Nome do campo" required>
                     <select name="tipos[]">
                        <option value="nome">Nome</option>
                        <option value="categoria">Categoria</option>
                        <option value="imagem">Imagem</option>
                        <option value="preco">Preço Dinheiro/Pix</option>
                        <option value="cartao">Cartão</option>
                        <option value="estoque">Estoque</option>
                     </select>`;
    container.appendChild(div);
}
</script>

<?php foreach($tabelas as $tabela): 
$campos = buscarCampos($conn, $tabela);
$registros = buscarRegistros($conn, $tabela);
?>
<div class="tabela-container">
    <h2><?php echo $tabela; ?></h2>
    <?php if(count($registros)==0): ?>
        <p><em>Não possui registros</em></p>
    <?php else: ?>
    <table>
        <tr>
            <?php foreach($campos as $campo): ?>
                <?php
                // Colorir badges por tipo de campo
                $classe = "";
                if(strpos($campo,"nome")!==false) $classe="badge-nome";
                elseif(strpos($campo,"categoria")!==false) $classe="badge-categoria";
                elseif(strpos($campo,"imagem")!==false) $classe="badge-imagem";
                elseif(strpos($campo,"preco")!==false) $classe="badge-preco";
                elseif(strpos($campo,"cartao")!==false) $classe="badge-cartao";
                elseif(strpos($campo,"estoque")!==false) $classe="badge-estoque";
                ?>
                <th><span class="badge <?php echo $classe;?>"><?php echo $campo; ?></span></th>
            <?php endforeach; ?>
            <th>Ações</th>
        </tr>
        <?php foreach($registros as $reg): ?>
        <tr>
            <?php foreach($campos as $campo): ?>
                <td>
                    <?php 
                    if(strpos($campo,"imagem")!==false && !empty($reg[$campo])): ?>
                        <img src="<?php echo $reg[$campo];?>" alt="" style="width:60px;">
                    <?php else: 
                        echo $reg[$campo];
                    endif;?>
                </td>
            <?php endforeach; ?>
            <td>
                <form style="display:inline;" method="post" action="acoes.php">
                    <input type="hidden" name="tabela" value="<?php echo $tabela;?>">
                    <input type="hidden" name="id" value="<?php echo $reg['id'];?>">
                    <button class="botao editar" name="acao" value="editar_registro">Editar</button>
                    <button class="botao remover" name="acao" value="remover_registro" onclick="return confirm('Tem certeza que deseja remover este registro?')">Remover</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    <form method="post" action="acoes.php">
        <input type="hidden" name="tabela" value="<?php echo $tabela;?>">
        <button class="botao adicionar" name="acao" value="adicionar_registro">Adicionar Registro</button>
        <button type="button" class="botao adicionar-campo" onclick="document.getElementById('addCampo-<?php echo $tabela;?>').style.display='block'">Adicionar Campo</button>
        <button class="botao remover-campo" name="acao" value="remover_campo">Remover Campo</button>
    </form>

    <!-- Form oculto adicionar campo -->
    <div id="addCampo-<?php echo $tabela;?>" style="display:none; margin-top:10px;">
        <form method="post" action="acoes.php">
            <input type="hidden" name="acao" value="adicionar_campo">
            <input type="hidden" name="tabela" value="<?php echo $tabela;?>">
            <input type="text" name="nome_campo" placeholder="Nome do campo" required>
            <select name="tipo_campo" required>
                <option value="nome">Nome</option>
                <option value="categoria">Categoria</option>
                <option value="imagem">Imagem</option>
                <option value="preco">Preço Dinheiro/Pix</option>
                <option value="cartao">Cartão</option>
                <option value="estoque">Estoque</option>
            </select>
            <button type="submit" class="botao adicionar">Adicionar</button>
            <button type="button" class="botao remover" onclick="document.getElementById('addCampo-<?php echo $tabela;?>').style.display='none'">Cancelar</button>
        </form>
    </div>
</div>
<?php endforeach; ?>
</body>
</html>
