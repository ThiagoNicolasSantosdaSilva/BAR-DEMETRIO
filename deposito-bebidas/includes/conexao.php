<?php
// Dados de conexão
$host = 'localhost';
$usuario = 'root';        // seu usuário do MySQL
$senha = '';              // sua senha do MySQL
$banco = 'deposito_bebidas'; // nome do banco de dados

// Criar conexão
$conn = mysqli_connect($host, $usuario, $senha, $banco);

// Verificar conexão
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Definir charset para UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
