<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Buscar usuário pelo e-mail
    $stmt = $conn->prepare("SELECT id, nome, email, senha, imagem FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_imagem'] = $usuario['imagem'];

            // Redirecionar para a página principal (ajuste conforme necessário)
            header("Location: index.php");
            exit;
        } else {
            // Senha incorreta
            $_SESSION['erro_login'] = "Senha incorreta.";
        }
    } else {
        // E-mail não encontrado
        $_SESSION['erro_login'] = "E-mail não encontrado.";
    }

    $stmt->close();
    $conn->close();
}

// Redireciona de volta para a página de login, se algo deu errado
header("Location: ./pages/login.php");
exit;