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
$id_usuario = 1; // ou pegue da sessão

$imagem_nome = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = uniqid('post_') . '.' . $ext;
    $caminho = 'uploads/' . $nome_arquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
        $imagem_nome = $caminho; // salva o caminho no banco
    }
}

$stmt = $conn->prepare("INSERT INTO postagens (id_usuario, titulo, texto, imagem) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $id_usuario, $titulo, $conteudo, $imagem_nome);
$stmt->execute();

header("Location: index.php");
exit;
?>
