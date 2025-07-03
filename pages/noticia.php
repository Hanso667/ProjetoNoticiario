<?php
session_start();
if (!isset($_SESSION['Mode'])) {
    $_SESSION['Mode'] = "Light";
}
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

    // PAGINAÇÃO
    $comentariosPorPagina = 5;
    $paginaAtual = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($paginaAtual - 1) * $comentariosPorPagina;

    // Comentários paginados
    $stmt = $conn->prepare("
        SELECT comentarios_postagem.*, usuarios.nome, usuarios.imagem 
        FROM comentarios_postagem 
        JOIN usuarios ON comentarios_postagem.id_usuario = usuarios.id 
        WHERE id_post = ? 
        ORDER BY data_comentario DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $id, $comentariosPorPagina, $offset);
    $stmt->execute();
    $comentarios = $stmt->get_result();
    $stmt->close();

    // Total de comentários
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM comentarios_postagem WHERE id_post = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $totalComentarios = $result['total'];
    $stmt->close();

    $totalPaginas = ceil($totalComentarios / $comentariosPorPagina);
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
    <?php if ($_SESSION['Mode'] == "Light"): ?>
        <link id="style" data-mode="light" rel="stylesheet" href="../src/css/noticia.css">
        <link id="style" rel="stylesheet" href="../src/css/header.css">
    <?php else: ?>
        <link id="style" data-mode="dark" rel="stylesheet" href="../src/css/noticiadark.css">
        <link id="style" rel="stylesheet" href="../src/css/headerdark.css">
    <?php endif; ?>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../src/img/Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .paginacao {
            margin-top: 20px;
            text-align: center;
        }

        .paginacao a {
            padding: 6px 12px;
            margin: 0 3px;
            text-decoration: none;
            border: 1px solid #ccc;
            color: #333;
            background-color: #f2f2f2;
        }

        .paginacao a.pagina-atual {
            font-weight: bold;
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }

        .ad {
            margin: 20px 0;
            width: 800px;
            height: 200px;
            border-radius: 15px;
        }
    </style>
</head>


<body>
    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></a>
                <h1 style="font-size: larger; text-decoration: none; color: white; border: none;" id="nome-pagina"><?php echo $noticia['titulo'] ?></h1>
            </div>

            <div class="header-right">



                <form id="form-search-all-usuarios" class="search" action="../pages/usuarios.php">
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

                <?php if (isset($_SESSION['Mode']) && $_SESSION['Mode'] == "Dark"): ?>
                    <button id="DarkButton" style="background-color: transparent; border: none; font-size: larger;">🌕</button>
                <?php else: ?>
                    <button id="DarkButton" style="background-color: transparent; border: none; font-size: larger;">🌑</button>
                <?php endif; ?>
            </div>
        </div>
    </header>


    <main style="max-width: 800px; margin: auto; padding: 20px;">

        <img src="https://placehold.co/800x200?text=ad" class="ad">
        </img>

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
            <p><em>por <?= htmlspecialchars($noticia['nome']) ?> em <?= date('d/m/Y H:i', strtotime($noticia['data_post'])) ?></em></p>
            <?php
            $imagemNoticia = !empty($noticia['imagem']) ? '.' . htmlspecialchars($noticia['imagem']) : '../src/img/NoImage.jpg';
            ?>
            <img src="<?= $imagemNoticia ?>" alt="Imagem da notícia" class="Imagem-postagem">
            <div class="conteudo-postagem"><?= $noticia['texto'] ?></div>
        </div>
        <?php
        // Contagens
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM likes_postagens WHERE id_post = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $likes = $stmt->get_result()->fetch_assoc()['total'];

        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM favoritos_postagens WHERE id_post = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $favoritos = $stmt->get_result()->fetch_assoc()['total'];

        // Verificações do usuário
        $jaCurtiu = $jaFavoritou = false;

        if (isset($_SESSION['usuario_id'])) {
            $usuarioId = $_SESSION['usuario_id'];

            $stmt = $conn->prepare("SELECT 1 FROM likes_postagens WHERE id_usuario = ? AND id_post = ?");
            $stmt->bind_param("ii", $usuarioId, $id);
            $stmt->execute();
            $jaCurtiu = $stmt->get_result()->num_rows > 0;

            $stmt = $conn->prepare("SELECT 1 FROM favoritos_postagens WHERE id_usuario = ? AND id_post = ?");
            $stmt->bind_param("ii", $usuarioId, $id);
            $stmt->execute();
            $jaFavoritou = $stmt->get_result()->num_rows > 0;
        }
        ?>

        <div style="margin-bottom: 15px;">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <button onclick="curtirPostagem()" id="btn-like">
                    <?= $jaCurtiu ? '❤️ Curtido' : '🤍 Curtir' ?>
                </button>
                <span id="like-count"><?= $likes ?></span>

                <button onclick="favoritarPostagem()" id="btn-fav">
                    <?= $jaFavoritou ? '⭐ Favoritado' : '☆ Favoritar' ?>
                </button>
                <span id="fav-count"><?= $favoritos ?></span>
            <?php else: ?>
                <p><a href="./login.php">Faça login para curtir ou favoritar</a></p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Comentário Form -->
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <section class="formulario-comentario">
                <h3>Deixe um comentário</h3>
                <form class="comment" action="../comentar.php" method="POST" onsubmit="return enviarComentario();">
                    <input type="hidden" name="id_post" value="<?= $id ?>">
                    <input type="hidden" name="comentario" id="comentario-hidden">
                    <div id="editor" class="quill-editor" style="height: 150px;"></div>
                    <button class="comment-button" type="submit" style="margin-top: 10px;">Enviar</button>
                </form>
            </section>
        <?php else: ?>
            <a href="./login.php" style="display: flex; flex-direction: column; justify-content: center; align-items: center; margin: 10px 0px; text-decoration: none;"><button class="comment-button">Faça login para comentar!</button></a>
        <?php endif; ?>

        <hr>

        <section class="comentarios">
            <h2>Comentários</h2>
            <?php if ($comentarios->num_rows > 0): ?>
                <?php while ($coment = $comentarios->fetch_assoc()): ?>
                    <div class="comentario" style="border: 1px solid #ccc; padding: 10px; margin: 10px 0; display: flex; gap: 10px;">
                        <img src="../src/img/<?= !empty($coment['imagem']) ? htmlspecialchars($coment['imagem']) : 'NoProfile.jpg' ?>" alt="Imagem do usuário" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
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

            <!-- PAGINAÇÃO -->
            <?php if ($totalPaginas > 1): ?>
                <div class="paginacao">
                    <?php if ($paginaAtual > 1): ?>
                        <a href="?id=<?= $id ?>&page=<?= $paginaAtual - 1 ?>">&laquo; Anterior</a>
                    <?php endif; ?>

                    <?php
                    $maxPaginas = 7;
                    $inicio = max(1, $paginaAtual - floor($maxPaginas / 2));
                    $fim = min($totalPaginas, $inicio + $maxPaginas - 1);
                    if ($fim - $inicio < $maxPaginas - 1) {
                        $inicio = max(1, $fim - $maxPaginas + 1);
                    }
                    for ($i = $inicio; $i <= $fim; $i++): ?>
                        <a href="?id=<?= $id ?>&page=<?= $i ?>" class="<?= $i == $paginaAtual ? 'pagina-atual' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($paginaAtual < $totalPaginas): ?>
                        <a href="?id=<?= $id ?>&page=<?= $paginaAtual + 1 ?>">Próxima &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

        <img src="https://placehold.co/800x200?text=ad" class="ad">
        </img>

    </main>

    <footer>
        <p>&copy; 2025 Portal de Notícias. Todos os direitos reservados.</p>
        <p>Desenvolvido por Hanso667.</p>
        <p>Contato: <a href="mailto:fabriciolacerdamoraes2005@gmail.com" class="footer-contato">fabriciolacerdamoraes2005@gmail.com</a></p>
        <div class="footer-social">
            <a href="https://github.com/Hanso667" class="social-btn" aria-label="Github"><i class="fab fa-github"></i></a>
            <a href="https://www.linkedin.com/in/fabricio-lacerda-moraes-991979300/" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <br>
        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == 0): ?>
            <a class="publicidade" href="./CadastroAnuncio.php"><button>Publicidade</button></a>
        <?php endif; ?>
    </footer>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        const quillComentario = new Quill('#editor', {
            theme: 'snow'
        });

        function enviarComentario() {
            const hiddenInput = document.getElementById('comentario-hidden');
            hiddenInput.value = quillComentario.root.innerHTML.trim();
            return hiddenInput.value.length > 0;
        }

        function toggleMenu() {
            const menu = document.getElementById('post-menu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        }

        let quillEditar = null;

        function ativarEdicao() {
            document.getElementById('visualizacao-postagem').style.display = 'none';
            document.getElementById('form-editar-postagem').style.display = 'block';

            if (!quillEditar) {
                quillEditar = new Quill('#editor-editar', {
                    theme: 'snow'
                });
                quillEditar.root.innerHTML = `<?= addslashes($noticia['texto']) ?>`;
            }

            document.getElementById('post-menu').style.display = 'none';
        }

        function salvarEdicao() {
            const hiddenInput = document.getElementById('texto-hidden-editar');
            hiddenInput.value = quillEditar.root.innerHTML.trim();
        }

        function curtirPostagem() {
            fetch('../likePost.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id_post=<?= $id ?>'
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Resposta do like:", data);
                    location.reload();
                })
                .catch(error => console.error('Erro no like:', error));
        }

        function favoritarPostagem() {
            fetch('../favoritarPost.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id_post=<?= $id ?>'
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Resposta do favorito:", data);
                    location.reload();
                })
                .catch(error => console.error('Erro no favorito:', error));
        }
    </script>

    <script>
        document.getElementById('DarkButton').addEventListener('click', function() {
            fetch('../toggle_mode.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(err => console.error('Erro ao trocar modo:', err));
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('../get_ads.php')
                .then(response => response.json())
                .then(imagens => {
                    const adElements = document.querySelectorAll('img.ad');
                    adElements.forEach((img, i) => {
                        // Se houver menos imagens do que elementos, reinicia a contagem
                        const index = i % imagens.length;
                        if (imagens.length != 0) {
                            img.src = "." + imagens[index];
                        } else {
                            img.src = "https://placehold.co/800x200?text=ad";
                        }
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar imagens de anúncios:', error);
                });
        });
    </script>

</body>

</html>