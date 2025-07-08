<?php
session_start();
include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['id_post'])) {
    http_response_code(403);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_post = intval($_POST['id_post']);

$stmt = $conn->prepare("SELECT * FROM likes_postagens WHERE id_usuario = ? AND id_post = ?");
$stmt->bind_param("ii", $id_usuario, $id_post);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Remover like
    $stmt = $conn->prepare("DELETE FROM likes_postagens WHERE id_usuario = ? AND id_post = ?");
} else {
    // Adicionar like
    $stmt = $conn->prepare("INSERT INTO likes_postagens (id_usuario, id_post) VALUES (?, ?)");
}
$stmt->bind_param("ii", $id_usuario, $id_post);
$stmt->execute();
echo "ok";