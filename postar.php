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

$titulo = $_POST['titulo'];
$conteudo = $_POST['conteudo'];
$id_usuario = $_SESSION['usuario_id']; // ← Aqui pega o ID do usuário logado

$imagem_nome = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = uniqid('post_') . '.' . $ext;
    $caminho = './src/img/' . $nome_arquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
        $imagem_nome = $caminho;
    }
}

$stmt = $conn->prepare("INSERT INTO postagens (id_usuario, titulo, texto, imagem) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $id_usuario, $titulo, $conteudo, $imagem_nome);
$stmt->execute();

header("Location: index.php");
exit;