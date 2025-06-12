<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Noticiario");
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

if (isset($_POST['id_post'], $_POST['comentario'])) {
    $id_post = intval($_POST['id_post']);
    $comentario = trim($_POST['comentario']);

    // ⚠️ Simulando usuário logado (substitua por $_SESSION['id_usuario'] em um sistema com login)
    $id_usuario = 1;

    if ($comentario !== '') {
        $stmt = $conn->prepare("INSERT INTO comentarios_postagem (id_post, id_usuario, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_post, $id_usuario, $comentario);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: pages/noticia.php?id=" . $id_post);
exit;
