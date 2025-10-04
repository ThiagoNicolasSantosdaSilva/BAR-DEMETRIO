<?php
session_start();
include "conexao.php";

// Função para mapear tipo de campo para SQL
function mapTipo($tipo){
    switch($tipo){
        case 'nome': return "VARCHAR(255) NOT NULL";
        case 'categoria': return "VARCHAR(255) NOT NULL";
        case 'imagem': return "VARCHAR(255) NOT NULL";
        case 'preco': return "DECIMAL(10,2) NOT NULL";
        case 'cartao': return "DECIMAL(10,2) NOT NULL";
        case 'estoque': return "INT(11) NOT NULL";
        default: return "VARCHAR(255) NOT NULL";
    }
}

$acao = $_POST['acao'] ?? '';

// ==================== CRIAR TABELA ====================
if($acao=='criar_tabela'){
    $nome_tabela = $_POST['nome_tabela'] ?? '';
    $campos = $_POST['campos'] ?? [];
    $tipos = $_POST['tipos'] ?? [];

    if(empty($nome_tabela) || empty($campos) || empty($tipos)) exit;

    $sql = "CREATE TABLE `$nome_tabela` (id INT(11) NOT NULL AUTO_INCREMENT, ";
    foreach($campos as $i => $campo){
        $tipoSQL = mapTipo($tipos[$i] ?? 'nome');
        $sql .= "`$campo` $tipoSQL, ";
    }
    $sql .= "PRIMARY KEY (id))";
    mysqli_query($conn, $sql);
    header("Location: painel.php");
    exit;
}

// ==================== ADICIONAR REGISTRO ====================
if($acao=='adicionar_registro'){
    $tabela = $_POST['tabela'] ?? '';
    if(empty($tabela)) exit;

    $res = mysqli_query($conn,"DESCRIBE `$tabela`");
    $campos = [];
    $valores = [];
    while($row=mysqli_fetch_assoc($res)){
        $campo = $row['Field'];
        if($campo=='id') continue;

        if(isset($_FILES[$campo]) && $_FILES[$campo]['error']==0){
            if(!is_dir("../uploads")) mkdir("../uploads", 0777, true);
            $ext = pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION);
            $nomeArquivo = "../uploads/".uniqid().".$ext";
            move_uploaded_file($_FILES[$campo]['tmp_name'],$nomeArquivo);
            $valores[] = "'$nomeArquivo'";
        } else {
            $valores[] = "'".($_POST[$campo] ?? '')."'";
        }
        $campos[] = "`$campo`";
    }

    $sql = "INSERT INTO `$tabela` (".implode(',',$campos).") VALUES (".implode(',',$valores).")";
    mysqli_query($conn,$sql);
    header("Location: painel.php");
    exit;
}

// ==================== EDITAR REGISTRO ====================
if($acao=='editar_registro'){
    $tabela = $_POST['tabela'] ?? '';
    $id = $_POST['id'] ?? '';
    if(empty($tabela) || empty($id)) exit;

    $res = mysqli_query($conn,"DESCRIBE `$tabela`");
    $updates = [];

    while($row=mysqli_fetch_assoc($res)){
        $campo = $row['Field'];
        if($campo=='id') continue;

        if(isset($_FILES[$campo]) && $_FILES[$campo]['error']==0){
            if(!is_dir("../uploads")) mkdir("../uploads", 0777, true);
            $ext = pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION);
            $nomeArquivo = "../uploads/".uniqid().".$ext";
            move_uploaded_file($_FILES[$campo]['tmp_name'],$nomeArquivo);
            $updates[] = "`$campo`='$nomeArquivo'";
        } else {
            $updates[] = "`$campo`='".($_POST[$campo] ?? '')."'";
        }
    }

    $sql = "UPDATE `$tabela` SET ".implode(',',$updates)." WHERE id=$id";
    mysqli_query($conn,$sql);
    header("Location: painel.php");
    exit;
}

// ==================== REMOVER REGISTRO ====================
if($acao=='remover_registro'){
    $tabela = $_POST['tabela'] ?? '';
    $id = $_POST['id'] ?? '';
    if(empty($tabela) || empty($id)) exit;

    mysqli_query($conn,"DELETE FROM `$tabela` WHERE id=$id");
    header("Location: painel.php");
    exit;
}

// ==================== ADICIONAR CAMPO ====================
if($acao=='adicionar_campo'){
    $tabela = $_POST['tabela'] ?? '';
    $campo = $_POST['nome_campo'] ?? '';
    $tipo = $_POST['tipo_campo'] ?? '';
    if(empty($tabela) || empty($campo) || empty($tipo)) exit;

    $tipoSQL = mapTipo($tipo);
    mysqli_query($conn,"ALTER TABLE `$tabela` ADD `$campo` $tipoSQL");
    header("Location: painel.php");
    exit;
}

// ==================== REMOVER CAMPO ====================
if($acao=='remover_campo'){
    $tabela = $_POST['tabela'] ?? '';
    $campo = $_POST['nome_campo'] ?? '';
    if(empty($tabela) || empty($campo)) exit;
    if($campo=='id') exit;

    mysqli_query($conn,"ALTER TABLE `$tabela` DROP COLUMN `$campo`");
    header("Location: painel.php");
    exit;
}
?>
