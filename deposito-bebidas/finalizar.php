<?php
session_start();
include 'includes/conexao.php';

// Recebe os dados do fetch
$dados = json_decode(file_get_contents("php://input"), true);

if(!$dados || !isset($dados['itens'])){
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Dados inválidos"
    ]);
    exit;
}

$itens = $dados['itens'];
$erros = [];

foreach($itens as $item){
    $id = intval($item['id']);
    $quantidade = intval($item['quantidade']);

    // Busca estoque atual
    $sql = "SELECT estoque FROM produtos WHERE id = $id";
    $res = mysqli_query($conn, $sql);

    if(mysqli_num_rows($res) === 0){
        $erros[] = "Produto ID $id não encontrado.";
        continue;
    }

    $produto = mysqli_fetch_assoc($res);

    if($produto['estoque'] < $quantidade){
        $erros[] = "Estoque insuficiente para o produto ID $id.";
        continue;
    }

    // Atualiza estoque
    $novoEstoque = $produto['estoque'] - $quantidade;
    $update = "UPDATE produtos SET estoque = $novoEstoque WHERE id = $id";
    mysqli_query($conn, $update);
}

if(count($erros) > 0){
    echo json_encode([
        "sucesso" => false,
        "mensagem" => implode(" | ", $erros)
    ]);
} else {
    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Compra finalizada e estoque atualizado!"
    ]);
}
?>
