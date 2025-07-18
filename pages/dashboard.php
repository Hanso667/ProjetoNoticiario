<?php
session_start();
$favoritosPorPagina = 4;
$paginaFavAtual = isset($_GET['fav_page']) && is_numeric($_GET['fav_page']) ? (int)$_GET['fav_page'] : 1;
$favOffset = ($paginaFavAtual - 1) * $favoritosPorPagina;

if (!isset($_SESSION['Mode'])) {
    $_SESSION['Mode'] = "Light";
}
include '../src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$user = null;
if ($userId >= 0) {
    $stmtUser = $conn->prepare("SELECT id, nome, email, imagem FROM usuarios WHERE id = ?");
    $stmtUser->bind_param('i', $userId);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    if ($resultUser && $resultUser->num_rows > 0) {
        $user = $resultUser->fetch_assoc();
    }
}

$totalFavoritos = 0;
$totalPaginasFavoritos = 1;

if (isset($_SESSION['usuario_id'])) {
    $stmtTotalFav = $conn->prepare("SELECT COUNT(*) as total FROM favoritos_postagens WHERE id_usuario = ?");
    $stmtTotalFav->bind_param("i", $userId);
    $stmtTotalFav->execute();
    $resultTotalFav = $stmtTotalFav->get_result();
    $totalFavoritos = $resultTotalFav->fetch_assoc()['total'];
    $totalPaginasFavoritos = max(1, ceil($totalFavoritos / $favoritosPorPagina));
}


if (isset($_SESSION['usuario_id'])) {
    $stmtFav = $conn->prepare("
        SELECT p.id, p.titulo, p.imagem
        FROM favoritos_postagens f
        JOIN postagens p ON f.id_post = p.id
        WHERE f.id_usuario = ?
        ORDER BY p.data_post DESC
        LIMIT ? OFFSET ?
    ");
    $stmtFav->bind_param("iii", $userId, $favoritosPorPagina, $favOffset);
    $stmtFav->execute();
    $resultFav = $stmtFav->get_result();

    $favoritos = [];
    while ($fav = $resultFav->fetch_assoc()) {
        $favoritos[] = $fav;
    }
}

// Paginação
$postagensPorPagina = 5;
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $postagensPorPagina;

// Busca as postagens do usuário com paginação
$sql = "
SELECT 
  p.id AS postagem_id,
  p.imagem AS imagem_postagem,
  p.titulo,
  p.texto,
  p.data_post,
  u.nome AS nome_autor_postagem,
  c.comentario,
  c.data_comentario,
  uc.nome AS nome_autor_comentario,
  uc.imagem AS imagem_comentario
FROM postagens p
JOIN usuarios u ON p.id_usuario = u.id
LEFT JOIN (
  SELECT cp1.*
  FROM comentarios_postagem cp1
  INNER JOIN (
    SELECT id_post, MAX(data_comentario) AS max_data
    FROM comentarios_postagem
    GROUP BY id_post
  ) cp2 ON cp1.id_post = cp2.id_post AND cp1.data_comentario = cp2.max_data
) c ON p.id = c.id_post
LEFT JOIN usuarios uc ON c.id_usuario = uc.id
WHERE p.id_usuario = ?
ORDER BY p.data_post DESC
LIMIT ? OFFSET ?";

$stmtPosts = $conn->prepare($sql);
$stmtPosts->bind_param('iii', $userId, $postagensPorPagina, $offset);
$stmtPosts->execute();
$resultPosts = $stmtPosts->get_result();

// Contar total de postagens
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM postagens WHERE id_usuario = ?");
$countStmt->bind_param('i', $userId);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalPostagens = $countResult->fetch_assoc()['total'];
$totalPaginas = ceil($totalPostagens / $postagensPorPagina);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link id="style" data-mode="light" rel="stylesheet" href="../src/css/dashboard.css">
    <link id="style" rel="stylesheet" href="../src/css/header.css">
    <link id="style" rel="stylesheet" href="../src/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?php
        if ($user) {
            echo "usuario de " . htmlspecialchars($user['nome']);
        } else {
            echo "Usuário não encontrado";
        }
        ?>
    </title>
    <link rel="icon" type="image/x-icon" href="../src/img/Logo.png">
</head>

<body>

    <div class="modal" id="Modal">

        <div class="modal-content" id="modal-content">
            <img src="../src/img/<?php echo htmlspecialchars($user['imagem'] ?? 'NoProfile.jpg') ?>">
        </div>

    </div>

    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></a>
                <h1 id="nome-pagina">Dashboard</h1>
            </div>

            <div class="header-right">



                <a href="./usuarios.php">
                    <button id="all_usuarios_button">Usuários</button>
                </a>

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

    <main>
        <div class="div-ad">
            <a href=""><img href="" src="" class="ad"></a>
        </div>

        <?php if ($user): ?>
            <section class="user-info" style="margin-bottom: 2rem;">
                <img src="../src/img/<?php echo htmlspecialchars($user['imagem'] ?? 'NoProfile.jpg'); ?>" alt="Foto de perfil" id="user_picture" />
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['usuario_id'] == $user['id'] || $_SESSION['usuario_id'] == 0)): ?>
                    <div style="margin-bottom: 1rem;">
                        <button onclick=edt() style="padding: 10px 20px; margin-right: 10px;">Editar Perfil</button>
                        <script>
                            function edt() {
                                document.getElementById('form-editar-usuario').style.display = 'block';
                                this.style.display = 'none';
                                document.getElementById('botao-deletar').style.display = 'none';
                            }
                        </script>
                        <?php if ($user['id'] != 0): ?>
                            <form class="deletar-usuario" id="botao-deletar" action="../deletarUser.php" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar seu perfil?')" style="display: inline;">
                                <input hidden name="userId" value="<?= $_SESSION['usuario_id'] ?>">
                                <input hidden name="id" value="<?= $user['id'] ?>">
                                <input hidden name="imagem" value="<?= htmlspecialchars($user['imagem']) ?>">
                                <button type="submit" style="padding: 10px 20px; background-color: crimson; color: white;">Deletar Perfil</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <form id="form-editar-usuario" action="../editarUser.php" method="POST" enctype="multipart/form-data" style="display: none; margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem; border-radius: 8px;">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($user['imagem']) ?>">

                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required style="display: block; margin-bottom: 10px; padding: 5px; width: 100%;">

                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="display: block; margin-bottom: 10px; padding: 5px; width: 100%;">

                        <label for="nova_imagem">Nova foto de perfil:</label>
                        <input type="file" name="nova_imagem" accept="image/*" style="margin-bottom: 10px;">

                        <button type="submit" style="padding: 10px 20px;">Salvar Alterações</button>
                    </form>
                <?php endif; ?>

                <div class="user-info-right">
                    <h1>Usuário: <?php echo htmlspecialchars($user['nome']); ?></h1>

                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </section>
            <div class="sidebar-favoritos">

                <h2>Favoritos</h2>

                <?php if ($totalPaginasFavoritos > 1): ?>
                    <div class="paginacao">
                        <?php if ($paginaFavAtual > 1): ?>
                            <a href="?id=<?= $userId ?>&fav_page=<?= $paginaFavAtual - 1 ?>&page=<?= $paginaAtual ?>">&larr; Anterior</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPaginasFavoritos; $i++): ?>
                            <a href="?id=<?= $userId ?>&fav_page=<?= $i ?>&page=<?= $paginaAtual ?>" class="<?= $i == $paginaFavAtual ? 'pagina-atual' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($paginaFavAtual < $totalPaginasFavoritos): ?>
                            <a href="?id=<?= $userId ?>&fav_page=<?= $paginaFavAtual + 1 ?>&page=<?= $paginaAtual ?>">Próxima &rarr;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM favoritos_postagens WHERE id_usuario = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $favoritosCount = $stmt->get_result()->fetch_assoc()['total'];
                ?>

                <?php if (!empty($favoritos)): ?>
                    <div class="favoritos" style="grid-template-columns: repeat(2,470px) ; grid-template-rows: repeat(<?php echo ceil($favoritosCount / 2) ?>,100px);">
                        <?php foreach ($favoritos as $fav): ?>
                            <li class="favorito-item">
                                <a href="./noticia.php?id=<?= $fav['id'] ?>">
                                    <img src="<?= $fav['imagem'] ? '.' . $fav['imagem'] : '../src/img/NoImage.jpg' ?>" alt="Imagem" class="favorito-imagem">
                                    <?= htmlspecialchars($fav['titulo']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Sem favoritos ainda.</p>
                <?php endif; ?>
            </div>

            <h2>Postagens recentes de <?php echo htmlspecialchars($user['nome']); ?>:</h2>

            <div class="card-view-noticias">
                <?php
                if ($resultPosts && $resultPosts->num_rows > 0) {
                    while ($row = $resultPosts->fetch_assoc()) {
                        $id = htmlspecialchars($row['postagem_id']);
                        $titulo = htmlspecialchars($row['titulo']);
                        $conteudo = $row['texto'];
                        $autor = htmlspecialchars($row['nome_autor_postagem']);
                        $data = date('d/m/Y', strtotime($row['data_post']));

                        $imagemPostagem = $row['imagem_postagem'] ? "." . $row['imagem_postagem'] : "../src/img/" . 'NoImage.jpg';

                        echo '
                    <div class="card-noticias" data-id="' . $id . '">
                        <div class="noticia">
                            <div class="noticia-div-imagem">
                                <img src="' . $imagemPostagem . '" class="noticia-imagem" alt="Imagem da postagem">
                            </div>
                            <div class="noticia-div-conteudo">
                                <h2 class="autor">Autor: ' . $autor . '</h2>
                                <h1 class="titulo">Título: ' . $titulo . '</h1>
                                <div class="conteudo">' . $conteudo . '</div>
                                <h3 class="data">' . $data . '</h3>
                            </div>
                        </div>';

                        if (!empty($row['comentario'])) {
                            $comentario = $row['comentario'];
                            $autorComentario = htmlspecialchars($row['nome_autor_comentario']);
                            $dataComentario = date('d/m/Y', strtotime($row['data_comentario']));
                            $imagemComentario = htmlspecialchars($row['imagem_comentario']);

                            echo '
                        <div class="comentario">
                            <div class="card-comentario">
                                <div class="comentario-div-imagem">
                                    <img src="../src/img/' . $imagemComentario . '" class="comentario-imagem" alt="Imagem do autor do comentário">
                                </div>
                                <div class="comentario-div-conteudo">
                                    <h2 class="comentario-autor">Autor: ' . $autorComentario . '</h2>
                                    <div class="comentario-conteudo">' . $comentario . '</div>
                                    <h3 class="comentario-data">' . $dataComentario . '</h3>
                                </div>
                            </div>
                        </div>';
                        }

                        echo '</div>';
                    }
                } else {
                    echo "<p>Este usuário não possui postagens.</p>";
                }
                ?>
            </div>
        <?php else: ?>
            <p>Usuário não encontrado ou ID inválido.</p>
        <?php endif; ?>
        <?php if ($totalPaginas > 1): ?>
            <div class="paginacao">

                <!-- Botão "Anterior" -->
                <?php if ($paginaAtual > 1): ?>
                    <a href="?id=<?= $userId ?>&page=<?= $paginaAtual - 1 ?>" class="seta">
                        &larr; Anterior
                    </a>
                <?php endif; ?>

                <?php
                // Lógica para exibir 9 páginas com a atual no meio (quando possível)
                $maxPaginas = 9;
                $meio = floor($maxPaginas / 2);

                // Calcula o início e fim do intervalo
                $inicio = max(1, $paginaAtual - $meio);
                $fim = $inicio + $maxPaginas - 1;

                // Ajusta caso o fim ultrapasse o total de páginas
                if ($fim > $totalPaginas) {
                    $fim = $totalPaginas;
                    $inicio = max(1, $fim - $maxPaginas + 1);
                }
                ?>

                <!-- Links das páginas -->
                <?php for ($i = $inicio; $i <= $fim; $i++): ?>
                    <a href="?id=<?= $userId ?>&page=<?= $i ?>" class="<?= $i == $paginaAtual ? 'pagina-atual' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Botão "Próxima" -->
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?id=<?= $userId ?>&page=<?= $paginaAtual + 1 ?>" class="seta">
                        Próxima &rarr;
                    </a>
                <?php endif; ?>

                <!-- Formulário de "Ir para página" -->
                <form method="get" class="form-ir-para" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $userId ?>">
                    <input type="number" name="page" min="1" max="<?= $totalPaginas ?>" placeholder="Página" required>
                    <button type="submit">Ir para</button>
                </form>

            </div>
        <?php endif; ?>

        <div class="div-ad">
            <a href=""><img href="" src="" class="ad"></a>
        </div>

    </main>

    <footer id="footer">

        <p>&copy; 2025 Portal de Notícias. Todos os direitos reservados.</p>

        <p>Desenvolvido por Hanso667.</p>

        <p>
            Contato: <a href="mailto:fabriciolacerdamoraes2005@gmail.com" class="footer-contato">fabriciolacerdamoraes2005@gmail.com</a><br>
        </p>

        <div class="footer-social">
            <a href="https://github.com/Hanso667" class="social-btn" aria-label="Github">
                <i class="fab fa-github"></i>
            </a>
            <a href="https://www.linkedin.com/in/fabricio-lacerda-moraes-991979300/" class="social-btn" aria-label="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
        </div>
        <br>
        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == 0): ?>
            <a class="publicidade" href="./CadastroAnuncio.php"><button>Anuncios</button></a>
        <?php else: ?>
            <a class="publicidade" href="./CadastroAnuncio-pedido.php"><button>Anuncios</button></a>
        <?php endif; ?>

    </footer>

    <script src="../src/scripts/dashScript.js"></script>
    <script src="../src/scripts/toggleDark.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Carrega anúncios em destaque para o modal
            fetch('../get_ads_destaque.php')
                .then(response => response.json())
                .then(destaques => {
                    const modalAd = document.querySelector('#myModal .ad');

                    if (destaques.length > 0) {
                        modalAd.src = destaques[0].imagem;
                        modalAd.parentElement.href = destaques[0].link || "#";
                    } else {
                        modalAd.src = "https://placehold.co/800x200?text=ad";
                        modalAd.parentElement.href = "#";
                    }
                })
                .catch(error => console.error('Erro ao carregar anúncios em destaque:', error));

            // Carrega todos os anúncios para os elementos com .ad (fora do modal)
            fetch('../get_ads.php')
                .then(response => response.json())
                .then(anuncios => {
                    const adElements = document.querySelectorAll('.ad:not(#myModal .ad)');

                    adElements.forEach((img, i) => {
                        if (anuncios.length === 0) {
                            img.src = "https://placehold.co/800x200?text=ad";
                            if (img.parentElement.tagName === "A") {
                                img.parentElement.href = "#";
                            }
                            return;
                        }

                        const index = i % anuncios.length;
                        const anuncio = anuncios[index];

                        img.src = "." + anuncio.imagem || "https://placehold.co/800x200?text=ad";
                        if (img.parentElement.tagName === "A") {
                            img.parentElement.href = anuncio.link || "#";
                        }
                    });
                })
                .catch(error => console.error('Erro ao carregar anúncios gerais:', error));
        });
    </script>

</body>

</html>