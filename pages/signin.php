<?php
$mensagem = '';
if (isset($_GET['mensagem'])) {
    // Evita XSS: sanitiza a mensagem para uso em JS
    $mensagem = htmlspecialchars($_GET['mensagem'], ENT_QUOTES, 'UTF-8');
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../src/scripts/Connection.php';
    $connection = new Connection();
    $conn = $connection->connectar();

    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $imagem = null;

    // Upload da imagem
    if (!empty($_FILES['imagem']['name'])) {
        $imagemNomeOriginal = basename($_FILES['imagem']['name']);
        $extensao = strtolower(pathinfo($imagemNomeOriginal, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($extensao, $permitidas)) {
            $novoNomeImagem = uniqid('img_', true) . '.' . $extensao;
            $imagemDestino = '../src/img/' . $novoNomeImagem;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $imagemDestino)) {
                $imagem = $novoNomeImagem;
            } else {
                header("Location: login.php?mensagem=Erro ao enviar imagem.");
                exit;
            }
        } else {
            header("Location: login.php?mensagem=Formato de imagem inválido.");
            exit;
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, imagem) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha, $imagem);
        $stmt->execute();

        header("Location: login.php?mensagem=Conta criada com sucesso!");
        exit;
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            header("Location: ./signin.php?mensagem=Nome ou e-mail já está em uso.");
        } else {
            header("Location: ./signin.php?mensagem=Erro ao criar conta.");
        }
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link rel="stylesheet" href="../src/css/login.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin - Criar Conta</title>
</head>
<body>

<header>
    <div class="header-container">
        <div class="header-left">
            <a href="../index.php"><button class="home-button">Home</button></a>
        </div>

        <div class="header-right">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="../logout.php"><button class="login-button">Logout</button></a>
            <?php endif; ?>
            <img src="../src/img/<?php echo $_SESSION['usuario_imagem'] ?? 'NoProfile.jpg'; ?>" class="profile-picture" alt="Foto de perfil">
        </div>
    </div>
</header>

<main>
    <form action="signin.php" method="POST" enctype="multipart/form-data" style="width: 50%; background: #fff8dc; padding: 20px; border: 2px dashed #003366; border-radius: 8px; box-shadow: 2px 2px 10px rgba(0, 51, 102, 0.2);">
        <h2 style="color: #003366; margin-bottom: 20px;">Criar nova conta</h2>

        <?php if (!empty($erro)): ?>
            <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>

        <input type="text" name="nome" placeholder="Seu nome" required class="input-titulo" style="margin-bottom: 10px;">
        <input type="email" name="email" placeholder="Seu e-mail" required class="input-titulo" style="margin-bottom: 10px;">
        <input type="password" name="senha" placeholder="Sua senha" required class="input-titulo" style="margin-bottom: 10px;">
        
        <label style="color: #003366;">Imagem de perfil (opcional):</label>
        <input type="file" name="imagem" accept="image/*" class="input-titulo" style="margin-bottom: 20px;">

        <button type="submit" class="botao-postar" style="width: 100%;">Criar Conta</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 Portal de Notícias. Todos os direitos reservados.</p>

    <p>Desenvolvido por Hanso667.</p>

    <p>
        Contato: <a href="mailto:fabriciolacerdamoraes2005@gmai.com" style="color: #ffffff;">fabriciolacerdamoraes2005@gmai.com</a><br>
    </p>

    <div style="margin-top: 10px;">
        <a href="https://github.com/Hanso667" class="social-btn" style="color: white; margin: 0 10px; font-size: 20px;" aria-label="Github">
            <i class="fab fa-github"></i>
        </a>
        <a href="https://www.linkedin.com/in/fabricio-lacerda-moraes-991979300/" class="social-btn" style="color: white; margin: 0 10px; font-size: 20px;" aria-label="LinkedIn">
            <i class="fab fa-linkedin-in"></i>
        </a>
    </div>

</footer>

<?php if (!empty($mensagem)): ?>
        <script>
            alert("<?= $mensagem ?>");
        </script>
    <?php endif; ?>
</body>
</html>
