<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$userId = $_POST['userId']; // ID do usuário logado
$id = $_POST['id'];         // ID do usuário a ser deletado
$imagem = $_POST['imagem']; // Imagem de perfil (opcional)

// 1. Apagar a imagem de perfil, se existir
if (!empty($imagem)) {
    $caminhoImagemPerfil = "./src/img/$imagem";
    if (file_exists($caminhoImagemPerfil)) {
        unlink($caminhoImagemPerfil);
    }
}

// 2. Buscar todas as imagens das postagens do usuário
$postStmt = $conn->prepare("SELECT imagem FROM postagens WHERE id_usuario = ?");
$postStmt->bind_param("i", $id);
$postStmt->execute();
$result = $postStmt->get_result();

while ($row = $result->fetch_assoc()) {
    $img = $row['imagem'];
    $caminhoImagemPost = $img;
    if (!empty($img) && file_exists($caminhoImagemPost)) {
        unlink($caminhoImagemPost);
    }
}
$postStmt->close();


// 4. Deletar o usuário do banco
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$conn->close();

// 5. Redirecionamento
if ($userId == $id) {
    // Usuário deletou a si mesmo
    header("Location: logout.php");
    exit;
} else {
    // Outro usuário deletado (admin, por exemplo)
    header("Location: index.php");
    exit;
}
?>
