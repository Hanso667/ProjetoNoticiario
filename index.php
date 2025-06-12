<?php
$host = 'localhost';
$db = 'noticiario';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$sql = 'SELECT 
  p.id AS postagem_id,
  p.titulo,
  p.texto,
  p.data_post,
  u.nome AS nome_autor_postagem,
  
  c.comentario,
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
        <section class="criar-postagem">
            <h2>Criar nova postagem</h2>
            <form action="postar.php" method="POST" onsubmit="return enviarPostagem();">
                <input type="text" name="titulo" placeholder="Título da postagem" required class="input-titulo">
                <div id="editor"></div>
                <input type="hidden" name="conteudo" id="conteudo">
                <button type="submit" class="botao-postar">Postar</button>
            </form>
        </section>

        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

        <script>
            const quill = new Quill('#editor', {
                theme: 'snow'
            });

            function enviarPostagem() {
                const conteudo = document.querySelector('input[name=conteudo]');
                conteudo.value = quill.root.innerHTML;
                return true;
            }
        </script>
        <div class="card-view-noticias">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagem = "./src/img/imagem1.jpg"; // imagem fixa por enquanto
                    $autor = htmlspecialchars($row['nome_autor_postagem']);
                    $id = htmlspecialchars($row['postagem_id']);
                    $titulo = htmlspecialchars($row['titulo']);
                    $conteudo = htmlspecialchars($row['texto']);
                    $data = date('d/m/Y', strtotime($row['data_post']));

                    echo '
        <div class="card-noticias" data-id=' . $id . '>
            <div class="noticia">
                <div class="noticia-div-imagem">
                    <img src="' . $imagem . '" class="noticia-imagem">
                </div>
                <div class="noticia-div-conteudo">
                    <h2 class="autor">Autor: ' . $autor . '</h2>
                    <h1 class="titulo">Titulo: ' . $titulo . '</h1>
                    <p class="conteudo">' . $conteudo . '</p>
                    <h3 class="data">' . $data . '</h3>
                </div>
            </div>';

                    // Exibir comentário apenas se existir
                    if (!empty($row['comentario'])) {
                        $Cimagem = "./src/img/imagem1.jpg";
                        $Cautor = htmlspecialchars($row['nome_autor_comentario']);
                        $Ccomentario = htmlspecialchars($row['comentario']);
                        $Cdata = date('d/m/Y', strtotime($row['data_comentario']));

                        echo '
            <div class="comentario">
                <div class="card-comentario">
                    <div class="comentario-div-imagem">
                        <img src="' . $Cimagem . '" class="comentario-imagem">
                    </div>
                    <div class="comentario-div-conteudo">
                        <h2 class="comentario-autor">Autor: ' . $Cautor . '</h2>
                        <p class="comentario-conteudo">' . $Ccomentario . '</p>
                        <h3 class="comentario-data">' . $Cdata . '</h3>
                    </div>
                </div>
            </div>';
                    }

                    echo '</div>'; // Fecha card-noticias
                }
            } else {
                echo "<p>Nenhuma postagem encontrada.</p>";
            }

            $conn->close();
            ?>

        </div>
    </main>

    <footer>
    </footer>

    <script src="./src/scripts/script.js"></script>
</body>

</html>