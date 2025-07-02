<?php
session_start();

if (!isset($_SESSION['Mode'])) {
  $_SESSION['Mode'] = "Light";
}

include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$searchTerm = isset($_GET['search_postagem']) ? trim($_GET['search_postagem']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recentes';

$orderBy = "p.data_post DESC"; // padrão: mais recentes

switch ($sort) {
  case 'antigas':
    $orderBy = "p.data_post ASC";
    break;
  case 'mais_curtidas':
    $orderBy = "(SELECT COUNT(*) FROM likes_postagens lp WHERE lp.id_post = p.id) DESC";
    break;
  case 'menos_curtidas':
    $orderBy = "(SELECT COUNT(*) FROM likes_postagens lp WHERE lp.id_post = p.id) ASC";
    break;
  case 'mais_favoritadas':
    $orderBy = "(SELECT COUNT(*) FROM favoritos_postagens lp WHERE lp.id_post = p.id) DESC";
    break;
  case 'menos_favoritadas':
    $orderBy = "(SELECT COUNT(*) FROM favoritos_postagens lp WHERE lp.id_post = p.id) ASC";
    break;
}

// Pagination setup
$postagensPorPagina = 3;
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $postagensPorPagina;

if ($searchTerm !== '') {
  $param = "%$searchTerm%";

  // Note: Can't bind LIMIT/OFFSET so embed safely after casting to int
  $sql = "SELECT 
      p.id AS postagem_id,
      p.imagem AS imagem_postagem,
      p.titulo,
      p.texto,
      p.data_post,
      u.nome AS nome_autor_postagem,
      c.comentario,
      c.data_comentario,
      uc.nome AS nome_autor_comentario,
      uc.imagem AS imagem_comentario,
      (SELECT COUNT(*) FROM likes_postagens lp WHERE lp.id_post = p.id) AS total_likes,
      (SELECT COUNT(*) FROM favoritos_postagens fp WHERE fp.id_post = p.id) AS total_favoritos
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
    WHERE p.titulo LIKE ?
    ORDER BY $orderBy
    LIMIT $postagensPorPagina OFFSET $offset";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $param);
  $stmt->execute();
  $result = $stmt->get_result();

  // Count total posts for pagination
  $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM postagens WHERE titulo LIKE ?");
  $countStmt->bind_param("s", $param);
  $countStmt->execute();
  $countResult = $countStmt->get_result();
} else {
  // No search term - fetch all posts with pagination

  $sql = "SELECT 
      p.id AS postagem_id,
      p.imagem AS imagem_postagem,
      p.titulo,
      p.texto,
      p.data_post,
      u.nome AS nome_autor_postagem,
      c.comentario,
      c.data_comentario,
      uc.nome AS nome_autor_comentario,
      uc.imagem AS imagem_comentario,
      (SELECT COUNT(*) FROM likes_postagens lp WHERE lp.id_post = p.id) AS total_likes,
      (SELECT COUNT(*) FROM favoritos_postagens fp WHERE fp.id_post = p.id) AS total_favoritos
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
    ORDER BY $orderBy
    LIMIT $postagensPorPagina OFFSET $offset";

  $result = $conn->query($sql);

  $countResult = $conn->query("SELECT COUNT(*) AS total FROM postagens");
}

$totalPostagens = $countResult->fetch_assoc()['total'];
$totalPaginas = ceil($totalPostagens / $postagensPorPagina);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <link rel="stylesheet" href="src/css/reset.css">
  <?php if ($_SESSION['Mode'] == "Light"): ?>
    <link id="style" data-mode="light" rel="stylesheet" href="./src/css/index.css">
    <link id="style" rel="stylesheet" href="./src/css/header.css">
  <?php else: ?>
    <link id="style" data-mode="dark" rel="stylesheet" href="./src/css/indexdark.css">
    <link id="style" rel="stylesheet" href="./src/css/headerdark.css">
  <?php endif; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal</title>
  <link rel="icon" type="image/x-icon" href="./src/img/Logo.png">
  <style>
    .tempo-info {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 16px;
      color: #333;
    }

    .tempo-icone {
      width: 50px;
      height: 50px;
    }

    .paginacao {
      text-align: center;
      margin: 20px 0;
    }

    .paginacao a {
      display: inline-block;
      margin: 0 5px;
      padding: 8px 12px;
      text-decoration: none;
      background-color: rgb(90, 90, 90);
      border-radius: 5px;
      color: white;
    }

    .pagina-atual {
      background-color: #2c3e50 !important;
      transform: scale(1.3);
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
        <a href="./index.php"><img src="./src/img/Logo.png" class="home-button"></a>
        <h1 id="nome-pagina">Portal do aluno</h1>
      </div>

      <div class="header-right">



        <form id="form-search-all-usuarios" class="search" action="./pages/usuarios.php">
          <button id="all_usuarios_button">Usuarios</button>
          <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="./logout.php"><button class="login-button">Logout</button></a>
          <?php else: ?>
            <a href="./pages/login.php"><button class="login-button">Login</button></a>
            <a href="./pages/signin.php"><button class="sigin-button">Signin</button></a>
          <?php endif; ?>
        </form>



        <?php if (isset($_SESSION['usuario_id'])): ?>
          <a href="./pages/dashboard.php?id=<?= $_SESSION['usuario_id'] ?>">
            <img src="./src/img/<?= $_SESSION['usuario_imagem'] ?>" class="profile-picture" alt="Foto de perfil">
          </a>
        <?php else: ?>
          <img src="./src/img/NoProfile.jpg" class="profile-picture" alt="Foto de perfil">
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

    <div id="previsao-tempo" style="display: flex; justify-content: center; align-items: center; gap: 10px; background-color: #e6e6e6;color: black; padding: 10px; border-radius: 5px; max-width: fit-content;">
      <p>Carregando previsão do tempo...</p>
    </div>

    <img src="https://placehold.co/800x200?text=ad" class="ad">
    </img>

    <br>
    <h1> Noticias recentes: </h1>

    <?php if ($totalPaginas > 1): ?>
      <div class="paginacao">

        <!-- Botão "Anterior" -->
        <?php if ($paginaAtual > 1): ?>
          <a href="?<?php echo "search_postagem=" . urlencode($searchTerm) . "&page=" . ($paginaAtual - 1) . "&sort=" . urlencode($sort); ?>" class="seta">
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
          <a href="?<?php echo "search_postagem=" . urlencode($searchTerm) . "&page=" . $i . "&sort=" . urlencode($sort); ?>"
            class="<?= $i == $paginaAtual ? 'pagina-atual' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <!-- Botão "Próxima" -->
        <?php if ($paginaAtual < $totalPaginas): ?>
          <a href="?<?php echo "search_postagem=" . urlencode($searchTerm) . "&page=" . ($paginaAtual + 1)  . "&sort=" . urlencode($sort); ?>" class="seta">
            Próxima &rarr;
          </a>
        <?php endif; ?>

        <!-- Formulário de "Ir para página" -->
        <form method="get" class="form-ir-para" style="display:inline;">
          <input type="hidden" name="search_postagem" value="<?= htmlspecialchars($searchTerm) ?>">
          <input type="number" name="page" min="1" max="<?= $totalPaginas ?>" placeholder="Página" required>
          <button type="submit">Ir para</button>
        </form>

      </div>
    <?php endif; ?>

    <form class="search" method="GET" action="./index.php" style="margin-bottom: 15px;">
      <input type="text" name="search_postagem" id="Search_postagem" placeholder=">Pesquisar Noticias recentes" value="<?= htmlspecialchars($searchTerm) ?>">


      <select style="height: 50px;" name="sort" onchange="this.form.submit()">
        <option value="recentes" <?= (isset($_GET['sort']) && $_GET['sort'] == 'recentes') ? 'selected' : '' ?>>Mais Recentes</option>
        <option value="antigas" <?= (isset($_GET['sort']) && $_GET['sort'] == 'antigas') ? 'selected' : '' ?>>Mais Antigas</option>
        <option value="mais_curtidas" <?= (isset($_GET['sort']) && $_GET['sort'] == 'mais_curtidas') ? 'selected' : '' ?>>Mais Curtidas</option>
        <option value="menos_curtidas" <?= (isset($_GET['sort']) && $_GET['sort'] == 'menos_curtidas') ? 'selected' : '' ?>>Menos Curtidas</option>
        <option value="mais_favoritadas" <?= (isset($_GET['sort']) && $_GET['sort'] == 'mais_favoritadas') ? 'selected' : '' ?>>Mais Favoritadas</option>
        <option value="menos_favoritadas" <?= (isset($_GET['sort']) && $_GET['sort'] == 'menos_favoritadas') ? 'selected' : '' ?>>Menos Favoritadas</option>
      </select>

      <button type="submit" id="Search_postagem_button"></button>
    </form>


    <div class="card-view-noticias">
      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $id = htmlspecialchars($row['postagem_id']);
          $titulo = htmlspecialchars($row['titulo']);
          $conteudo = $row['texto'];
          $autor = htmlspecialchars($row['nome_autor_postagem']);
          $data = date('d/m/Y', strtotime($row['data_post']));
          $imagemPostagem = $row['imagem_postagem'] ? htmlspecialchars($row['imagem_postagem']) : './src/img/noImage.jpg';
          $totalLikes = $row['total_likes'];
          $totalFavoritos = $row['total_favoritos'];

          echo '<div class="card-noticias" data-id="' . $id . '">
            <div class="noticia">
              <div class="noticia-div-imagem">
                <img src="' . $imagemPostagem . '" class="noticia-imagem">
              </div>
              <div class="noticia-div-conteudo">
                <h2 class="autor">Autor: ' . $autor . '</h2>
                <h1 class="titulo">Título: ' . $titulo . '</h1>
                <div class="conteudo">' . $conteudo . '</div>    
                <span><i class="fa fa-thumbs-up"></i> Likes: ' . $totalLikes . '</span>
                <span><i class="fa fa-star"></i> Favoritos: ' . $totalFavoritos . '</span>
                <h3 class="data">' . $data . '</h3>
              </div>
            </div>';

          if (!empty($row['comentario'])) {
            $comentario = $row['comentario'];
            $autorComentario = htmlspecialchars($row['nome_autor_comentario']);
            $dataComentario = date('d/m/Y', strtotime($row['data_comentario']));
            $imagemComentario = htmlspecialchars($row['imagem_comentario']);

            echo '<div class="comentario">
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
      ?>
    </div>

    <?php if ($totalPaginas > 1): ?>
      <div class="paginacao">

        <!-- Botão "Anterior" -->
        <?php if ($paginaAtual > 1): ?>
          <a href="?<?php echo "search_postagem=" . urlencode($searchTerm) . "&page=" . ($paginaAtual - 1) . "&sort=" . urlencode($sort); ?>" class="seta">
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
          <a href="?<?php echo "search_postagem=" . urlencode($searchTerm) . "&page=" . $i  . "&sort=" . urlencode($sort); ?>"
            class="<?= $i == $paginaAtual ? 'pagina-atual' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <!-- Botão "Próxima" -->
        <?php if ($paginaAtual < $totalPaginas): ?>
          <a href="?<?php echo "search_postagem=" . urlencode($searchTerm) . "&page=" . ($paginaAtual + 1)  . "&sort=" . urlencode($sort); ?>" class="seta">
            Próxima &rarr;
          </a>
        <?php endif; ?>

        <!-- Formulário de "Ir para página" -->
        <form method="get" class="form-ir-para" style="display:inline;">
          <input type="hidden" name="search_postagem" value="<?= htmlspecialchars($searchTerm)  ?>">
          <input type="number" name="page" min="1" max="<?= $totalPaginas ?>" placeholder="Página" required>
          <button type="submit">Ir para</button>
        </form>

      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['usuario_id'])): ?>
      <form class="criar-postagem" action="postar.php" method="POST" onsubmit="return enviarPostagem();" enctype="multipart/form-data">
        <h1> Faça uma Postagem!</h1><br>
        <input type="text" name="titulo" placeholder="Título da postagem" required class="input-titulo">
        <div id="editor"></div>
        <input type="hidden" name="conteudo" id="conteudo">
        <label for="imagem">Selecione a imagem</label>
        <input type="file" name="imagem" accept="image/*">
        <button type="submit" class="botao-postar">Postar</button>
      </form>
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

    <?php endif; ?>

    <img src="" class="ad">
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

    <?php if (isset($_SESSION['usuario_id'])): ?>
      <a class="publicidade" href="./pages/CadastroAnuncio.php"><button>Publicidade</button></a>
    <?php else: ?>
      <a class="publicidade" href="./pages/login.php"><button>Publicidade</button></a>
    <?php endif; ?>

  </footer>

  <script src="./src/scripts/script.js"></script>
  <script>
    document.getElementById('DarkButton').addEventListener('click', function() {
      fetch('toggle_mode.php')
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
    document.addEventListener("DOMContentLoaded", function() {
      const apiKey = '1cd1f2df98496f64d72e8e747b61d2ed'; // <--- Substitua pela sua chave da OpenWeather

      // Verifica se o navegador permite geolocalização
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric&lang=pt_br`)
              .then(response => response.json())
              .then(data => {
                const climaDiv = document.getElementById('previsao-tempo');
                const temperatura = data.main.temp.toFixed(1);
                const descricao = data.weather[0].description;
                const cidade = data.name;
                const icone = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;

                climaDiv.innerHTML = `
                <div class="tempo-info">
                  <img src="${icone}" alt="Ícone do clima" class="tempo-icone">
                  <span><strong>${cidade}</strong>: ${descricao}, ${temperatura}°C</span>
                </div>
              `;
              })
              .catch(error => {
                console.error('Erro ao obter clima:', error);
                document.getElementById('previsao-tempo').innerHTML = "<p>Erro ao carregar o clima.</p>";
              });
          },
          function(error) {
            console.warn('Erro na geolocalização:', error);
            document.getElementById('previsao-tempo').innerHTML = "<p>Localização não permitida.</p>";
          }
        );
      } else {
        document.getElementById('previsao-tempo').innerHTML = "<p>Navegador não suporta geolocalização.</p>";
      }
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      fetch('get_ads.php')
        .then(response => response.json())
        .then(imagens => {
          const adElements = document.querySelectorAll('img.ad');
          adElements.forEach((img, i) => {
            // Se houver menos imagens do que elementos, reinicia a contagem
            const index = i % imagens.length;
            if (imagens.length != 0) {
              img.src = imagens[index];
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