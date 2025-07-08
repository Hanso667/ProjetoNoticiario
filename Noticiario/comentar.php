<?php
session_start();
include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./pages/login.php");
    exit;
}

if (isset($_POST['id_post'], $_POST['comentario'])) {
    $id_post = intval($_POST['id_post']);
    $comentario = trim($_POST['comentario']);
    $id_usuario = $_SESSION['usuario_id']; // ← Pega o ID do usuário logado

    if ($comentario !== '') {
        $stmt = $conn->prepare("INSERT INTO comentarios_postagem (id_post, id_usuario, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_post, $id_usuario, $comentario);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: pages/noticia.php?id=" . $id_post);
exit;
