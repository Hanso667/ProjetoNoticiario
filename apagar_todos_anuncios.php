<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

include './src/scripts/Connection.php';

$conn = (new Connection())->connectar();

// Verifica se há anúncios inativos
$checkStmt = $conn->prepare("SELECT imagem FROM anuncios WHERE ativo = 0");
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Apagar as imagens físicas
    while ($row = $result->fetch_assoc()) {
        $imagem = $row['imagem'];
        $caminhoImagem = "./src/img/ads/" . $imagem;
        if (!empty($imagem) && file_exists($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }
    $checkStmt->close();

    // Agora apagar os anúncios inativos do banco de dados
    $deleteStmt = $conn->prepare("DELETE FROM anuncios WHERE ativo = 0");
    if ($deleteStmt->execute()) {
        header("Location: ./pages/CadastroAnuncio.php?apagado=1");
    } else {
        echo "Erro ao apagar anúncios: " . $deleteStmt->error;
    }
    $deleteStmt->close();
} else {
    $checkStmt->close();
    // Nenhum anúncio inativo
    header("Location: ./pages/CadastroAnuncio.php?apagado=-1");
}

$conn->close();
?>
