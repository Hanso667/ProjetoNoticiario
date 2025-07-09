<?php
session_start();
include './src/scripts/Connection.php';

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso não autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio'])) {
    $id_anuncio = intval($_POST['id_anuncio']);

    $conn = (new Connection())->connectar();
    $stmt = $conn->prepare("UPDATE anuncios SET aprovado = 1 WHERE id = ?");
    $stmt->bind_param("i", $id_anuncio);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: ./pages/CadastroAnuncio.php?aprovado=1");
    exit;
}
?>
