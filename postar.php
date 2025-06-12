<?php
$host = 'localhost';
$db = 'noticiario';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$titulo = $_POST['titulo'];
$conteudo = $_POST['conteudo'];

// Buscar ID do usuário "Anônimo"
$sql = "SELECT id FROM usuarios WHERE nome = 'Anônimo' LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$id_anonimo = $row['id'] ?? null;

if ($id_anonimo) {
    $stmt = $conn->prepare("INSERT INTO postagens (id_usuario, titulo, texto) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_anonimo, $titulo, $conteudo);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Erro ao postar: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Erro: usuário 'Anônimo' não encontrado.";
}

$conn->close();
?>