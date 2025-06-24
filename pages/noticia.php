<?php
session_start();

include '../src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

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
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></a>Noticia | <?= $noticia['titulo'] ?>
            </div>

            <div class="header-right">
                <form class="search" action="../pages/usuarios.php">
                    <button id="all_usuarios_button">Usuarios</button>
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

        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $noticia['postagem_usuario_id']): ?>
            <div style="position: relative; display: inline-block; margin-bottom: 10px;">
                <button type="button" onclick="toggleMenu()" style="padding: 5px 10px;">...</button>
                <div id="post-menu" style="display: none; position: absolute; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 5px rgba(0,0,0,0.2); right: 0; z-index: 10;">
                    <form action="../deletarPost.php" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta postagem?')" style="margin: 0;">
                        <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
                        <input type="hidden" name="imagem" value="<?= $noticia['imagem'] ?>">
                        <button type="submit" style="display: block; width: 100%; border: none; background: none; padding: 10px; text-align: left;">Deletar</button>
                    </form>
                    <button type="button" onclick="ativarEdicao()" style="display: block; width: 100%; border: none; background: none; padding: 10px; text-align: left;">Editar</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form editar -->
        <form id="form-editar-postagem" action="../editarPost.php" method="POST" enctype="multipart/form-data" style="display: none;">
            <input type="hidden" name="id" value="<?= $noticia['id'] ?>">
            <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($noticia['imagem']) ?>">

            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo-editar" value="<?= htmlspecialchars($noticia['titulo']) ?>" style="width: 100%; padding: 5px; font-size: 1.2em; margin-bottom: 10px;">

            <label for="conteudo">Conteúdo:</label>
            <div id="editor-editar" class="quill-editor" style="height: 200px; margin-bottom: 10px;"></div>

            <input type="hidden" name="texto" id="texto-hidden-editar">

            <label for="nova_imagem">Nova imagem:</label>
            <input type="file" name="nova_imagem" accept="image/*" style="margin-bottom: 10px;">

            <button type="submit" onclick="salvarEdicao()" style="padding: 10px 20px;">Salvar Alterações</button>
        </form>


        <!-- Visualização padrão -->
        <div id="visualizacao-postagem">
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

            <div class="conteudo-postagem"><?= $noticia['texto'] ?></div>
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
                            <div class="comentario-conteudo"><?= $coment['comentario'] ?></div>
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
        <p>Contato: <a href="mailto:fabriciolacerdamoraes2005@gmail.com" class="footer-contato">fabriciolacerdamoraes2005@gmail.com</a></p>
        <div class="footer-social">
            <a href="https://github.com/Hanso667" class="social-btn" aria-label="Github"><i class="fab fa-github"></i></a>
            <a href="https://www.linkedin.com/in/fabricio-lacerda-moraes-991979300/" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </footer>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="../src/scripts/noticiaScript.js"></script>

</body>

</html>