<?php

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
  c.imagem AS imagem_comentario,
  c.data_comentario,
  uc.nome AS nome_autor_comentario

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal</title>
</head>
<body>

<header>
    <ul class="login">
        <li><button class="login-button">Login</button></li>
        <li><button class="sigin-button">Signin</button></li>
        <li><img src="./src/img/" class="profile-picture"></li>
    </ul>
</header>

<main>
    <form action="postar.php" method="POST" onsubmit="return enviarPostagem();" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título da postagem" required class="input-titulo">
        <div id="editor"></div>
        <input type="hidden" name="conteudo" id="conteudo">
        <input type="file" name="imagem" accept="image/*" required>
        <button type="submit" class="botao-postar">Postar</button>
    </form>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        const quill = new Quill('#editor', { theme: 'snow' });
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
                $imagemPostagem = !empty($row['imagem_postagem']) ? 'src/img/' . htmlspecialchars($row['imagem_postagem']) : './src/img/imagem1.jpg';

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
                    $imagemComentario = !empty($row['imagem_comentario']) ? 'uploads/' . htmlspecialchars($row['imagem_comentario']) : './src/img/imagem1.jpg';

                    echo '
                    <div class="comentario">
                        <div class="card-comentario">
                            <div class="comentario-div-imagem">
                                <img src="' . $imagemComentario . '" class="comentario-imagem">
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

<footer></footer>

<script src="./src/scripts/script.js"></script>
</body>
</html>
