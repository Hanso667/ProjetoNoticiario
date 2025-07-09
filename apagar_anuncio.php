<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./pages/login.php");
    exit;
}

include './src/scripts/Connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio'])) {
    $id_anuncio = intval($_POST['id_anuncio']);
    $imagem = $_POST['imagem'];

    unlink("./src/img/ads/$imagem");


    $conn = (new Connection())->connectar();

    // Verifica se o anúncio pertence ao usuário
    $stmt = $conn->prepare("DELETE FROM anuncios WHERE id = ? AND ativo = 0");
    $stmt->bind_param("i", $id_anuncio);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: ./pages/CadastroAnuncio.php?apagado_individual=1");
    exit;
}
