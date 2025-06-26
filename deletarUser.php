<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$userId = $_POST['userID'];
$id = $_POST['id'];
$imagem = $_POST['imagem'];

// Remove a imagem
unlink("./src/img/$imagem");

// Deleta o usuário do banco
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($userID != 0) {
    header("Location: logout.php");
    exit;
}

header("Location: index.php");
exit;
?>