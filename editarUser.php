<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (isset($_POST['id'], $_POST['nome'], $_POST['email'], $_SESSION['usuario_id'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $usuario_id = $_SESSION['usuario_id'];
    $imagem_atual = $_POST['imagem_atual'];

    // Verificar se o usuário está editando o próprio perfil
    if ($id == $usuario_id) {
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
                $novo_nome = uniqid('user_') . '.' . $extensao;
                $destino = './src/img/' . $novo_nome;

                if (move_uploaded_file($tmp_arquivo, $destino)) {
                    // Deletar imagem antiga se não for NoProfile.jpg
                    if (!empty($imagem_atual) && $imagem_atual != 'NoProfile.jpg' && file_exists('../src/img/' . $imagem_atual)) {
                        unlink('../src/img/' . $imagem_atual);
                    }

                    $nova_imagem_path = $novo_nome;
                }
            } else {
                echo "Tipo de imagem inválido.";
                exit;
            }
        }

        // Atualizar no banco
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, imagem = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $email, $nova_imagem_path, $id);

        if ($stmt->execute()) {
            // Atualizar sessão
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_email'] = $email;
            $_SESSION['usuario_imagem'] = $nova_imagem_path;

            header("Location: ./pages/dashboard.php?id=$id&edit=success");
            exit;
        } else {
            echo "Erro ao atualizar o usuário.";
        }

        $stmt->close();
    } else {
        echo "Você não tem permissão para editar este usuário.";
    }
} else {
    echo "Dados incompletos.";
}
?>
