<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./pages/login.php");
    exit;
}

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anunciante = $_SESSION['usuario_id'];
    $dias = (int)$_POST['dias'];
    $validade = date('Y-m-d', strtotime("+{$dias} days"));
    $imagem = $_FILES['imagem'];
    $preco_total = $dias * 2.00;

    $imagem_nome = time() . '_' . basename($imagem['name']);
    $caminho_destino = './src/img/ads/' . $imagem_nome;

    if (move_uploaded_file($imagem['tmp_name'], $caminho_destino)) {
        $stmt = $conn->prepare("INSERT INTO anuncios (imagem, anunciante, validade, ativo) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sis", $imagem_nome, $anunciante, $validade);

        if ($stmt->execute()) {
            // ✅ Redirect with success flag and total value
            header("Location: ./pages/CadastroAnuncio.php?success=1&valor=" . number_format($preco_total, 2, '.', ''));
            exit;
        } else {
            $mensagem = "<p style='color: red;'>Erro ao cadastrar anúncio.</p>";
        }

        $stmt->close();
    } else {
        $mensagem = "<p style='color: red;'>Erro ao fazer upload da imagem.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Anúncio</title>
    <link re
