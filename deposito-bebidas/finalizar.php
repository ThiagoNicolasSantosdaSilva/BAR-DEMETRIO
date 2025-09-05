<?php
session_start();
include 'includes/conexao.php';

header('Content-Type: application/json');

// Pega os dados do fetch
$dados = json_decode(file_get_contents("php://input"), true);

if (!isset($dados['itens']) || empty($dados['itens'])) {
    echo json_encode(["sucesso" => false, "mensagem" => "Nenhum item recebido."]);
    exit;
}

$itens = $dados['itens'];
$erros = [];

foreach ($itens as $item) {
    $id = intval($item['id']);
    $quantidade = intval($item['quantidade']);

    // Verifica estoque atual
    $res = mysqli_query($conn, "SELECT estoque FROM produtos WHERE id=$id");
    $row = mysqli_fetch_assoc($res);

    if ($row && $row['estoque'] >= $quantidade) {
        // Atualiza o estoque
        $novoEstoque = $row['estoque'] - $quantidade;
        mysqli_query($conn, "UPDATE produtos SET estoque=$novoEstoque WHERE id=$id");
    } else {
        $erros[] = "Estoque insuficiente para o produto ID $id.";
    }
}

if (empty($erros)) {
    echo json_encode(["sucesso" => true, "mensagem" => "Compra finalizada com sucesso! 🎉"]);
} else {
    echo json_encode(["sucesso" => false, "mensagem" => implode(" ", $erros)]);
}
