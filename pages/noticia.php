<?php
session_start();

include '../src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Buscar a notícia
    $stmt = $conn->prepare('
        SELECT postagens.*, usuarios.nome, usuarios.id as postagem_usuario_id  
        FROM postagens 
        JOIN usuarios ON postagens.id_usuario = usuarios.id 
        WHERE postagens.id = ?
    ');
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$noticia) {
        echo "Notícia não encontrada.";
        exit;
    }

    // Buscar comentários 
    $stmt = $conn->prepare("
    SELECT comentarios_postagem.*, usuarios.nome, usuarios.imagem 
    FROM comentarios_postagem 
    JOIN usuarios ON comentarios_postagem.id_usuario = usuarios.id 
    WHERE id_post = ? 
    ORDER BY data_comentario DESC
");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $comentarios = $stmt->get_result();
    $stmt->close();
} else {
    echo "ID não informado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?></title>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link rel="stylesheet" href="../src/css/noticia.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>

    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><button class="home-button">Home</button></a>
            </div>

            <div class="header-right">

                <form class="search" action="../pages/usuarios.php">
                    <button id="all_usuarios_button"> Usuarios </button>
                </form>

                <form class="search" action="../pages/usuarios.php">
                    <input type="text" name="id" id="Search_usuario" placeholder=">Pesquisar usuarios">
                    <button id="Search_usuario_button"> </button>
                </form>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="../logout.php"><button class="login-button">Logout</button></a>
                <?php else: ?>
                    <a href="../pages/login.php"><button class="login-button">Login</button></a>
                    <a href="../pages/signin.php"><button class="sigin-button">Signin</button></a>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="../pages/dashboard.php?id=<?= $_SESSION['usuario_id'] ?>">
                        <img src="../src/img/<?= $_SESSION['usuario_imagem'] ?>" class="profile-picture" alt="Foto de perfil">
                    </a>
                <?php else: ?>
                    <img src="../src/img/NoProfile.jpg" class="profile-picture" alt="Foto de perfil">
                <?php endif; ?>
            </div>
        </div>
    </header>


    <main style="max-width: 800px; margin: auto; padding: 20px;">
        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id']  == $noticia['postagem_usuario_id']): ?>
            <form class="deletar-postagem" action="../deletarPost.php" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta postagem?')">
                <input hidden name="id" value="<?php echo $noticia['id']?>">
                <input hidden name="imagem" value="<?php echo $noticia['imagem'] ?>">
                <button type="submit">Deletar</button>
            </form>
        <?php endif; ?>

        <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <p>
            <em>por <?= htmlspecialchars($noticia['nome']) ?> em
                <?= date('d/m/Y H:i', strtotime($noticia['data_post'])) ?></em>
        </p>

        <?php
        $imagemNoticia = !empty($noticia['imagem'])
            ? '.' . htmlspecialchars($noticia['imagem'])
            : '../src/img/NoImage.jpg';
        ?>
        <img src="<?= $imagemNoticia ?>" alt="Imagem da notícia" class="Imagem-postagem">

        <div class="conteudo-postagem">
            <?= $noticia['texto'] ?>
        </div>

        <hr>

        <section class="formulario-comentario">
            <h3>Deixe um comentário</h3>
            <form class="comment" action="../comentar.php" method="POST" onsubmit="return enviarComentario();">
                <input type="hidden" name="id_post" value="<?= $id ?>">
                <input type="hidden" name="comentario" id="comentario-hidden">
                <div id="editor" class="quill-editor" style="height: 150px;"></div>
                <button class="comment-button" type="submit" style="margin-top: 10px;">Enviar</button>
            </form>
        </section>

        <hr>

        <section class="comentarios">
            <h2>Comentários</h2>

            <?php if ($comentarios->num_rows > 0): ?>
                <?php while ($coment = $comentarios->fetch_assoc()): ?>
                    <div class="comentario" style="border: 1px solid #ccc; padding: 10px; margin: 10px 0; display: flex; gap: 10px; align-items: flex-start;">
                        <?php if (!empty($coment['imagem'])): ?>
                            <img src="../src/img/<?= htmlspecialchars($coment['imagem']) ?>" alt="Imagem do usuário" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <img src="../src/img/NoProfile.jpg" alt="Sem imagem" style="width: 75px; height: 75px; object-fit: cover; border-radius: 50%;">
                        <?php endif; ?>

                        <div>
                            <strong><?= htmlspecialchars($coment['nome']) ?></strong>
                            <small>em <?= date('d/m/Y H:i', strtotime($coment['data_comentario'])) ?></small>
                            <div><?= $coment['comentario'] ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Seja o primeiro a comentar!</p>
            <?php endif; ?>
        </section>


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

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        function enviarComentario() {
            const hiddenInput = document.getElementById('comentario-hidden');
            hiddenInput.value = quill.root.innerHTML.trim();
            return hiddenInput.value.length > 0;
        }
    </script>

</body>

</html>