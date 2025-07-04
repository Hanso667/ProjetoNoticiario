<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

include './src/scripts/Connection.php';

$id_usuario = $_SESSION['usuario_id'];
$conn = (new Connection())->connectar();

$stmt = $conn->prepare("DELETE FROM anuncios WHERE anunciante = ? AND ativo = 0");
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    header("Location: ./pages/CadastroAnuncio.php?apagado=1");
} else {
    echo "Erro ao apagar anúncios: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
