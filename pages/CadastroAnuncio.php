<?php
session_start();

if (!isset($_SESSION['Mode'])) {
    $_SESSION['Mode'] = "Light";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro Anuncios</title>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link id="style" data-mode="light" rel="stylesheet" href="../src/css/cdAnuncio.css">
    <link id="style" rel="stylesheet" href="../src/css/header.css">
    <link id="style" rel="stylesheet" href="../src/css/footer.css">
    <link rel="icon" type="image/x-icon" href="../src/img/Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            background-color: rgb(90, 90, 90);
            border-radius: 5px;
            color: white;
        }

        .pagina-atual {
            background-color: #2c3e50 !important;
            transform: scale(1.3);
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></button></a>
                <h1 style="color: white; font-size: larger; border: none;" id="nome-pagina"> Anuncios</h1>
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
                <?php if ($_SESSION['Mode'] == "Dark"): ?>
                    <button id="DarkButton" style="background-color: transparent; border: none; font-size: xx-large;">🌕</button>
                <?php else: ?>
                    <button id="DarkButton" style="background-color: transparent; border: none; font-size: xx-large;">🌑</button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main style="max-width: 800px; margin: auto; padding: 20px;">

        <?php
        include '../src/scripts/Connection.php';

        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != 0 ) {
            echo "<p style='color: red;'>Acesso negado. Faça login como administrador.</p>";
            exit;
        }

        // CONEXÃO
        $conn = (new Connection())->connectar();

        // BUSCAR ANÚNCIOS PENDENTES
        $sql = "SELECT a.*, u.nome, u.email FROM anuncios a 
        JOIN usuarios u ON u.id = a.anunciante 
        WHERE a.aprovado = 0
        ORDER BY a.id DESC";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            echo "<h2>Anúncios Pendentes de Aprovação</h2>";

            while ($anuncio = $result->fetch_assoc()):
        ?>
                <div style="border: 1px solid #ccc; padding: 15px; margin: 20px 0;">
                    <img src="../src/img/ads/<?= $anuncio['imagem'] ?>" alt="Imagem do anúncio" style="width: 100%; max-width: 600px;"><br>
                    <p><strong>ID:</strong> <?= $anuncio['id'] ?></p>
                    <p><strong>Anunciante:</strong> <?= htmlspecialchars($anuncio['nome']) ?></p>
                    <p><strong>Email:</strong><a href="mailto:<?= htmlspecialchars($anuncio['email']) ?>"><?= htmlspecialchars($anuncio['email']) ?></a> </p>
                    <p><strong>Link:</strong> <?= $anuncio['link'] ? "<a href='{$anuncio['link']}' target='_blank'>{$anuncio['link']}</a>" : "Nenhum" ?></p>
                    <p><strong>Validade:</strong> <?= date("d/m/Y", strtotime($anuncio['validade'])) ?></p>
                    <p><strong>Destaque:</strong> <?= $anuncio['destaque'] ? "Sim" : "Não" ?></p>

                    <form action="../aprovar_anuncio.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_anuncio" value="<?= $anuncio['id'] ?>">
                        <button type="submit" style="background-color: green; color: white; padding: 5px 10px;">✔️ Aprovar</button>
                    </form>

                    <form action="../apagar_anuncio.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este anúncio?');" style="display: inline;">
                        <input type="hidden" name="id_anuncio" value="<?= $anuncio['id'] ?>">
                        <input type="hidden" name="imagem" value="<?= $anuncio['imagem'] ?>">
                        <button type="submit" style="background-color: red; color: white; padding: 5px 10px;">🗑️ Excluir</button>
                    </form>
                </div>
        <?php
            endwhile;
        else:
            echo "<p>Nenhum anúncio pendente de aprovação.</p>";
        endif;

        $conn->close();
        ?>

        <form action="../apagar_todos_anuncios.php" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar todos os anúncios inativos?');" style="margin-top: 20px;">
            <button type="submit" style="background-color: red; color: white; padding: 10px; margin-top: 100px;">🗑️ Apagar Todos os Inativos</button>
        </form>

        <?php
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == 0) {

            if (!isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != 0) {
                die("Usuário não autenticado.");
            }

            $connection = new Connection();
            $conn = $connection->connectar(); // Este deve retornar um objeto mysqli

            $id_usuario = $_SESSION['usuario_id'];

            // Paginação
            $por_pagina = 3;
            $pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $offset = ($pagina_atual - 1) * $por_pagina;

            // Total de anúncios inativos
            $total_sql = $conn->prepare("SELECT COUNT(*) as total FROM anuncios where ativo = 0");
            $total_sql->execute();
            $result_total = $total_sql->get_result();
            $total_registros = $result_total->fetch_assoc()['total'];
            $total_sql->close();

            $total_paginas = ceil($total_registros / $por_pagina);

            // Buscar anúncios inativos com paginação
            $anuncios_inativos = [];
            $stmt = $conn->prepare("SELECT 
    a.id AS id_anuncio,
    a.imagem,
    a.link,
    a.validade,
    a.destaque,
    a.ativo,
    u.id AS id_usuario,
    u.nome AS nome_usuario,
    u.email,
    u.imagem
FROM anuncios AS a
LEFT JOIN usuarios AS u ON u.id = a.anunciante
WHERE a.ativo = 0
ORDER BY a.validade DESC
LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $por_pagina, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $anuncios_inativos[] = $row;
                }
            }

            $stmt->close();

            if ($anuncios_inativos):
        ?>
                <h2 style="margin-top: 40px;">Anúncios Inativos</h2>
                <?php foreach ($anuncios_inativos as $anuncio): ?>
                    <div style="border: 1px solid #ccc; padding: 10px; margin-top: 10px;">
                        <p><strong>ID:</strong> <?= $anuncio['id_anuncio'] ?></p>
                        <p><strong>Anunciante:</strong> <?= $anuncio['nome_usuario'] ?></p>
                        <p><strong>Link:</strong> <?= $anuncio['link'] ?: 'Nenhum' ?></p>
                        <p><strong>Validade anterior:</strong> <?= date("d/m/Y", strtotime($anuncio['validade'])) ?></p>
                        <form action="../reativar_anuncio.php" method="POST" style="margin-bottom: 10px;">
                            <input type="hidden" name="id_anuncio" value="<?= $anuncio['id_anuncio'] ?>">
                            <label for="dias_<?= $anuncio['id_anuncio'] ?>">Nova validade:</label>
                            <select name="dias" id="dias_<?= $anuncio['id_anuncio'] ?>" required>
                                <option value="">Selecione</option>
                                <option value="3">3 dias (R$6,00)</option>
                                <option value="7">7 dias (R$14,00)</option>
                                <option value="15">15 dias (R$30,00)</option>
                                <option value="30">30 dias (R$60,00)</option>
                            </select>
                            <button type="submit">Reativar</button>
                        </form>

                        <!-- Pagar individualmente -->
                        <form action="../apagar_anuncio.php" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar este anúncio?');" style="display:inline;">
                            <input type="hidden" name="id_anuncio" value="<?= $anuncio['id_anuncio'] ?>">
                            <input type="hidden" name="imagem" value="<?= $anuncio['imagem'] ?>">
                            <button type="submit" style="background-color: red; color: white;">🗑️ Apagar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <?php if ($total_paginas > 1): ?>
                    <div style="margin-top: 20px; text-align: center;">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <a href="?pagina=<?= $i ?>" style="margin: 0 5px; <?= $i == $pagina_atual ? 'font-weight:bold; text-decoration:underline;' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
        <?php
            else:
                echo "<p>Nenhum anúncio inativo.</p>";
            endif;
        }
        ?>


        <script>
            function calcularValor() {
                const dias = parseInt(document.getElementById('dias').value || 0);
                const destaque = document.getElementById('destaque').checked;
                const precoPorDia = 2.00;
                const valorBase = dias * precoPorDia;
                const valorDestaque = destaque ? 20.00 : 0;
                const valorTotal = valorBase + valorDestaque;

                document.getElementById('valor_total').textContent = `Valor total: R$${valorTotal.toFixed(2).replace('.', ',')}`;
                return true;
            }
        </script>

    </main>

    <footer id="footer">
        <p>&copy; 2025 Portal de Notícias. Todos os direitos reservados.</p>
        <p>Desenvolvido por Hanso667.</p>
        <p>Contato: <a href="mailto:fabriciolacerdamoraes2005@gmail.com" class="footer-contato">fabriciolacerdamoraes2005@gmail.com</a></p>
        <div class="footer-social">
            <a href="https://github.com/Hanso667" class="social-btn" aria-label="Github"><i class="fab fa-github"></i></a>
            <a href="https://www.linkedin.com/in/fabricio-lacerda-moraes-991979300/" class="social-btn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <br>

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
    </script>
    <script src="../src/scripts/toggleDark.js"></script>
    <script>
        window.addEventListener("keydown", function(event) {
            if (event.keyCode === 116) {
                event.preventDefault();
                const baseUrl = window.location.origin + window.location.pathname;
                window.location.href = baseUrl;
            }
        });
    </script>

    <?php
    if (isset($_GET['success']) && $_GET['success'] === "1") {
        echo "<script>alert('anuncio cadastrado com sucesso')</script>";
    } ?>

    <?php
    if (isset($_GET['apagado_individual']) && $_GET['apagado_individual'] === "1") {
        echo "<script>alert('Anúncio apagado com sucesso.');</script>";
    } ?>

    <?php
    if (isset($_GET['apagado']) && $_GET['apagado'] === "1") {
        echo "<script>alert('Anúncios apagados com sucesso.');</script>";
    } ?>

    <?php
    if (isset($_GET['apagado']) && $_GET['apagado'] === "-1") {
        echo "<script>alert('nenhum anuncio inativo encontrado');</script>";
    } ?>

</body>

</html>