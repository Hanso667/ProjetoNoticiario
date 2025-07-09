<?php
session_start();
include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anunciante = $_SESSION['usuario_id'];

    // Salvar a imagem
    if (!empty($_FILES['imagem']['name'])) {
        $imagemNome = basename($_FILES['imagem']['name']);
        $imagemCaminho = './src/img/ads/' . $imagemNome;

        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $imagemCaminho)) {
            die("Erro ao enviar a imagem.");
        }
    } else {
        die("Imagem é obrigatória.");
    }

    // Obter os outros campos
    $link = isset($_POST['link']) ? $_POST['link'] : null;
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $dias = isset($_POST['dias']) ? intval($_POST['dias']) : 0;

    // Calcular validade
    $dataValidade = date('Y-m-d', strtotime("+$dias days"));

    // Inserir no banco
    $stmt = $conn->prepare("INSERT INTO anuncios (imagem, anunciante, validade, ativo, aprovado, link, destaque) 
                            VALUES (?, ?, ?, 1, 0, ?, ?)");
    $stmt->bind_param("sissi", $imagemNome, $anunciante, $dataValidade, $link, $destaque);

    if ($stmt->execute()) {
        header("Location: ./pages/CadastroAnuncio-pedido.php?success=1");
        exit();
    } else {
        echo "Erro ao cadastrar anúncio: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
