<?php
session_start();

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
    <link rel="stylesheet" href="../src/css/dashboard.css">
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

    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></button></a>
                <h1 id="nome-pagina">Dashboard</h1>
            </div>

            <div class="header-right">

                <form class="search" action="../pages/usuarios.php">
                    <button id="all_usuarios_button"> Usuarios </button>
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

    <main>
        <?php if ($user): ?>
            <section class="user-info" style="margin-bottom: 2rem;">
                <img src="../src/img/<?php echo htmlspecialchars($user['imagem'] ?? 'NoProfile.jpg'); ?>" alt="Foto de perfil" />
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
            <div class="paginacao" style="text-align:center; margin: 20px 0;">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?id=<?= $userId ?>&page=<?= $i ?>"
                        class="<?= $i == $paginaAtual ? 'pagina-atual' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </main>

    <footer>
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

    </footer>

    <script src="../src/scripts/dashScript.js"></script>
</body>

</html>