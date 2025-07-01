<?php
session_start();

include '../src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$searchTerm = $_GET['id'] ?? '';

// Paginação
$usuariosPorPagina = 10;
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $usuariosPorPagina;

if ($searchTerm !== '') {
    // Busca com LIKE
    $likeTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare("SELECT id, nome, imagem FROM usuarios WHERE nome LIKE ? LIMIT ? OFFSET ?");
    $stmt->bind_param('sii', $likeTerm, $usuariosPorPagina, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Conta total de resultados
    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE nome LIKE ?");
    $countStmt->bind_param('s', $likeTerm);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
    $totalUsuarios = $totalResult->fetch_assoc()['total'];
} else {
    // Sem busca
    $stmt = $conn->prepare("SELECT id, nome, imagem FROM usuarios LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $usuariosPorPagina, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $countResult = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
    $totalUsuarios = $countResult->fetch_assoc()['total'];
}

$totalPaginas = ceil($totalUsuarios / $usuariosPorPagina);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="stylesheet" href="../src/css/reset.css">
    <?php if ($_SESSION['Mode'] == "Light"): ?>
        <link id="style" data-mode="light" rel="stylesheet" href="../src/css/usuarios.css">
    <?php else: ?>
        <link id="style" data-mode="dark" rel="stylesheet" href="../src/css/usuariosdark.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários</title>
    <link rel="icon" type="image/x-icon" href="../src/img/Logo.png">
    <style>
        .paginacao {
            text-align: center;
            margin: 20px 0;
        }

        .paginacao a {
            display: inline-block;
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            background-color: #f2f2f2;
            border-radius: 5px;
            color: #333;
        }

        .pagina-atual {
            background-color: #007bff;
            color: blue !important;
            transform: scale(1.3);
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></a>
                <h1 style="font-size: larger;" id="nome-pagina">Usuários</h1>
            </div>

            <div class="header-right">
                <form class="search" action="../pages/usuarios.php">
                    <button id="all_usuarios_button"> Usuários </button>
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
                <?php if ($_SESSION['Mode'] == "Dark"): ?>
                    <button id="DarkButton" style="background-color: transparent; border: none; font-size: larger;">🌕</button>
                <?php else: ?>
                    <button id="DarkButton" style="background-color: transparent; border: none; font-size: larger;">🌑</button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <br>

        <form class="search" action="../pages/usuarios.php" method="GET">
            <input type="text" name="id" id="Search_usuario" placeholder=">Pesquisar usuários" value="<?= htmlspecialchars($searchTerm) ?>">
            <button id="Search_usuario_button">🔍</button>
        </form>

        <h1>
            <?= $searchTerm !== ''
                ? 'Usuários encontrados para "' . htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') . '"'
                : 'Lista de todos os usuários'
            ?>
        </h1>

        <ul>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $imagem = htmlspecialchars($row['imagem'] ?? 'NoProfile.jpg');
                    $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
                    echo "<li><a href=\"./dashboard.php?id=$id\"><img class=\"userPic\" src=\"../src/img/$imagem\"> $nome </a></li>";
                }
            } else {
                echo "<li>Nenhum usuário encontrado.</li>";
            }
            ?>
        </ul>

        <?php if ($totalPaginas > 1): ?>
            <div class="paginacao">

                <!-- Botão "Anterior" -->
                <?php if ($paginaAtual > 1): ?>
                    <a href="?<?php echo "Search_usuario=" . urlencode($searchTerm) . "&page=" . ($paginaAtual - 1); ?>" class="seta">
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
                    <a href="?<?php echo "Search_usuario=" . urlencode($searchTerm) . "&page=" . $i; ?>"
                        class="<?= $i == $paginaAtual ? 'pagina-atual' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Botão "Próxima" -->
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?<?php echo "Search_usuario=" . urlencode($searchTerm) . "&page=" . ($paginaAtual + 1); ?>" class="seta">
                        Próxima &rarr;
                    </a>
                <?php endif; ?>

                <!-- Formulário de "Ir para página" -->
                <form method="get" class="form-ir-para" style="display:inline;">
                    <input type="hidden" name="Search_usuario" value="<?= htmlspecialchars($searchTerm) ?>">
                    <input type="number" name="page" min="1" max="<?= $totalPaginas ?>" placeholder="Página" required>
                    <button type="submit">Ir para</button>
                </form>

            </div>
        <?php endif; ?>
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
            <a class="publicidade" href=""><button>Publicidade</button></a>
        <?php else: ?>
            <a class="publicidade" href="../pages/login.php"><button>Publicidade</button></a>
        <?php endif; ?>
    </footer>

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

</body>

</html>