<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (isset($_POST['id'], $_POST['titulo'], $_POST['texto'], $_SESSION['usuario_id'])) {
    $id = intval($_POST['id']);
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    $usuario_id = $_SESSION['usuario_id'];
    $imagem_atual = $_POST['imagem_atual'];

    // Verificar se o usuário é dono da postagem
    $stmt = $conn->prepare("SELECT id_usuario FROM postagens WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($result && $result['id_usuario'] == $usuario_id || $usuario_id == 0 ) {
        $nova_imagem_path = $imagem_atual;

        // Se enviou nova imagem
        if (isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] === UPLOAD_ERR_OK) {
            $nome_arquivo = $_FILES['nova_imagem']['name'];
            $tmp_arquivo = $_FILES['nova_imagem']['tmp_name'];
            $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));

            // Validar extensão da imagem
            $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extensao, $permitidas)) {
                // Nome único
                $novo_nome = uniqid('img_') . '.' . $extensao;
                $destino = './src/img/' . $novo_nome;

                if (move_uploaded_file($tmp_arquivo, $destino)) {
                    // Deletar imagem antiga se existir e for válida
                    if (!empty($imagem_atual) && file_exists('.' . $imagem_atual)) {
                        unlink('.' . $imagem_atual);
                    }

                    $nova_imagem_path = './src/img/' . $novo_nome;
                }
            } else {
                echo "Tipo de imagem inválido.";
                exit;
            }
        }

        // Atualizar no banco
        $stmt = $conn->prepare("UPDATE postagens SET titulo = ?, texto = ?, imagem = ? WHERE id = ?");
        $stmt->bind_param("sssi", $titulo, $texto, $nova_imagem_path, $id);

        if ($stmt->execute()) {
            header("Location: ./pages/noticia.php?id=$id&edit=success");
            exit;
        } else {
            echo "Erro ao atualizar a postagem.";
        }

        $stmt->close();
    } else {
        echo "Você não tem permissão para editar esta postagem.";
    }
} else {
    echo "Dados incompletos.";
}
?>
