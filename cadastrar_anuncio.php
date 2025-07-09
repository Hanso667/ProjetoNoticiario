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
    $link = isset($_POST['link']) ? $_POST['link'] : null;
    $destaque = isset($_POST['destaque']) ? 1 : 0;

    $preco_total = $dias * 2.00 + ($destaque ? 20.00 : 0.00);

    $imagem_nome = time() . '_' . basename($imagem['name']);
    $caminho_destino = './src/img/ads/' . $imagem_nome;

    if (move_uploaded_file($imagem['tmp_name'], $caminho_destino)) {
        $stmt = $conn->prepare("INSERT INTO anuncios (imagem, anunciante, validade, ativo, aprovado, link, destaque) VALUES (?, ?, ?, 1, 0, ?, ?)");
        $stmt->bind_param("sissi", $imagem_nome, $anunciante, $validade, $link, $destaque);

        if ($stmt->execute()) {
            $email_usuario = $_SESSION['usuario_email']; // Ou outra fonte
            $valor_final = number_format($preco_total, 2, '.', '');
            header("Location: ./criar_pagamento.php?valor={$valor_final}&email=" . urlencode($email_usuario) . "&anuncio_id={$id_anuncio}");
            exit;
        } else {
            $mensagem = "<p style='color: red;'>Erro ao cadastrar anúncio.</p>";
        }

        $stmt->close();
    } else {
        $mensagem = "<p style='color: red;'>Erro ao fazer upload da imagem.</p>";
    }
}
