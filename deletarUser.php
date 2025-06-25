<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$id = $_POST['id'];
$imagem = $_POST['imagem'];

// Remove a imagem
unlink("./src/img/$imagem");

// Deleta o usuário do banco
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Se o ID não for 12, redireciona para logout
if ($id != 12) {
    header("Location: logout.php");
    exit;
}

// Caso seja o ID 12, redireciona para algum lugar ou exibe uma mensagem
header("Location: index.php"); // exemplo de redirecionamento alternativo
exit;
?>
