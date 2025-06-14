<?php
session_start();

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$sql = 'SELECT 
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

ORDER BY p.data_post DESC;';

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="stylesheet" href="src/css/reset.css">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal</title>
</head>

<body>

    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="./index.php"><button class="home-button">Home</button></a>
            </div>

            <div class="header-right">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="./logout.php"><button class="login-button">Logout</button></a>
                <?php else: ?>
                    <a href="./pages/login.php"><button class="login-button">Login</button></a>
                    <a href="./pages/signin.php"><button class="sigin-button">Signin</button></a>
                <?php endif; ?>
                <img src="./src/img/<?php echo $_SESSION['usuario_imagem'] ?? 'NoProfile.jpg'; ?>" class="profile-picture" alt="Foto de perfil">
            </div>
        </div>
    </header>

    <main>

        <form class="criar-postagem" action="postar.php" method="POST" onsubmit="return enviarPostagem();" enctype="multipart/form-data" style="margin-top: 20px;">
            <h1> Faça uma Postagem!</h1><br>
            <input type="text" name="titulo" placeholder="Título da postagem" required class="input-titulo">
            <div id="editor"></div>
            <input type="hidden" name="conteudo" id="conteudo">
            <label for="imagem">Selecione a imagem</label>
            <input type="file" name="imagem" accept="image/*">
            <button type="submit" class="botao-postar">Postar</button>
        </form>

        <br><h1> Postagens recentes: </h1>

        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            const quill = new Quill('#editor', {
                theme: 'snow'
            });

            function enviarPostagem() {
                document.querySelector('input[name=conteudo]').value = quill.root.innerHTML;
                return true;
            }
        </script>

        <div class="card-view-noticias">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = htmlspecialchars($row['postagem_id']);
                    $titulo = htmlspecialchars($row['titulo']);
                    $conteudo = $row['texto'];
                    $autor = htmlspecialchars($row['nome_autor_postagem']);
                    $data = date('d/m/Y', strtotime($row['data_post']));

                    // Imagem da postagem
                    $imagemPostagem = htmlspecialchars($row['imagem_postagem']);

                    echo '
                <div class="card-noticias" data-id="' . $id . '">
                    <div class="noticia">
                        <div class="noticia-div-imagem">
                            <img src="' . $imagemPostagem . '" class="noticia-imagem">
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
                                <img src="./src/img/' . $imagemComentario . '" class="comentario-imagem">
                            </div>
                            <div class="comentario-div-conteudo">
                                <h2 class="comentario-autor">Autor: ' . $autorComentario . '</h2>
                                <div class="comentario-conteudo">' . $comentario . '</div>
                                <h3 class="comentario-data">' . $dataComentario . '</h3>
                            </div>
                        </div>
                    </div>';
                    }

                    echo '</div>'; // card-noticias
                }
            } else {
                echo "<p>Nenhuma postagem encontrada.</p>";
            }

            $conn->close();
            ?>
        </div>
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

    <script src="./src/scripts/script.js"></script>
</body>

</html>