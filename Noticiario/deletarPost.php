<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$id = $_POST['id'];
$imagem = $_POST['imagem'];

unlink("./src/img/$imagem");

$stmt = $conn->prepare("delete from postagens where id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();



header("Location: index.php");
exit;
?>
