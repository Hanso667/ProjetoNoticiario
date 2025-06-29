<?php
session_start();

include '../src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro Anuncios</title>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link rel="stylesheet" href="../src/css/noticia.css">
    <link rel="icon" type="image/x-icon" href="../src/img/Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><img src="../src/img/Logo.png" class="home-button"></a>
                <h1 id="nome-pagina">Usuários</h1>
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
            </div>
        </div>
    </header>

    <main style="max-width: 800px; margin: auto; padding: 20px;">

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

</body>

</html>