<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require './src/scripts/Connection.php';
    
    $connection = new Connection();
    $conn = $connection->connectar(); // retorna um objeto mysqli

    $id_anuncio = $_POST['id_anuncio'];
    $dias = intval($_POST['dias']);
    $nova_validade = date('Y-m-d', strtotime("+$dias days"));
    $usuario_id = $_SESSION['usuario_id'];

    $stmt = $conn->prepare("UPDATE anuncios SET validade = ?, ativo = 1 WHERE id = ? AND anunciante = ?");
    $stmt->bind_param("sii", $nova_validade, $id_anuncio, $usuario_id);

    if ($stmt->execute()) {
        header("Location: ./pages/CadastroAnuncio.php?reativado=1");
        exit;
    } else {
        echo "Erro ao atualizar anúncio: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
