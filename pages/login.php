

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link rel="stylesheet" href="../src/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal</title>
</head>

<body>

    <header>
        <div class="header-container">
            <div class="header-left">
                <a href="../index.php"><button class="home-button">Home</button></a>
            </div>

            <div class="header-right">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="../logout.php"><button class="login-button">Logout</button></a>
                <?php endif; ?>
                <img src="../src/img/<?php echo $_SESSION['usuario_imagem'] ?? 'NoProfile.jpg'; ?>" class="profile-picture" alt="Foto de perfil">
            </div>
        </div>
    </header>

    <main style="display: flex; justify-content: center; align-items: center;">
        <form action="../autenticar.php" method="POST" style="background-color: #fffdf5; border: 2px solid #003366; border-radius: 10px; padding: 40px; width: 400px; box-shadow: 2px 2px 10px rgba(0, 51, 102, 0.2); display: flex; flex-direction: column; gap: 20px;">
            <h2 style="font-size: 24px; color: #003366; text-align: center;">Login</h2>

            <label for="email" style="font-weight: bold; color: #003366;">E-mail:</label>
            <input type="email" name="email" id="email" required style="padding: 10px; border: 2px solid #003366; border-radius: 5px;">

            <label for="senha" style="font-weight: bold; color: #003366;">Senha:</label>
            <input type="password" name="senha" id="senha" required style="padding: 10px; border: 2px solid #003366; border-radius: 5px;">

            <button type="submit" class="botao-postar">Entrar</button>

            <p style="text-align: center;">Não tem uma conta? <a href="signin.php" style="color: #003366;">Crie uma aqui</a></p>
        </form>
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

    
</body>

</html>