<?php
// Dados de conexão - ajuste conforme seu XAMPP
$host = "localhost";      // servidor do banco
$user = "root";           // usuário do MySQL
$pass = "";               // senha do MySQL (no XAMPP geralmente vazio)
$db   = "bar_do_demetrio"; // nome do banco

// Cria a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Define charset para evitar problemas com acentos
$conn->set_charset("utf8mb4");
?>
