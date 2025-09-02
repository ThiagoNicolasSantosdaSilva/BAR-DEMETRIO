<?php
// Inicia a sessão caso ainda não tenha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Função para obter os dados do usuário logado
function usuarioLogado($conn) {
    if (!isLoggedIn()) {
        return null;
    }

    $usuario_id = $_SESSION['usuario_id'];
    $query = "SELECT id, nome, email FROM usuarios WHERE id = $usuario_id LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Função para fazer login
function login($conn, $email, $senha) {
    $email = mysqli_real_escape_string($conn, $email);
    $senha = mysqli_real_escape_string($conn, $senha);

    // Aqui considere que a senha está armazenada com password_hash
    $query = "SELECT id, senha FROM usuarios WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $usuario = mysqli_fetch_assoc($result);
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            return true;
        }
    }
    return false;
}

// Função para logout
function logout() {
    session_unset();
    session_destroy();
}
?>
