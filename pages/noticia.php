<?php
session_start(); // Se quiser lidar com login depois

include '../src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Buscar a notícia
    $stmt = $conn->prepare("
        SELECT postagens.*, usuarios.nome 
        FROM postagens 
        JOIN usuarios ON postagens.id_usuario = usuarios.id 
        WHERE postagens.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$noticia) {
        echo "Notícia não encontrada.";
        exit;
    }

    // Buscar comentários
    $stmt = $conn->prepare("
        SELECT comentarios_postagem.*, usuarios.nome 
        FROM comentarios_postagem 
        JOIN usuarios ON comentarios_postagem.id_usuario = usuarios.id 
        WHERE id_post = ? 
        ORDER BY data_comentario DESC
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $comentarios = $stmt->get_result();
    $stmt->close();
} else {
    echo "ID não informado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?></title>
    <link rel="stylesheet" href="../src/css/reset.css">
    <link rel="stylesheet" href="../src/css/noticia.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>

    <main style="max-width: 800px; margin: auto; padding: 20px;">
        <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <p>
            <em>por <?= htmlspecialchars($noticia['nome']) ?> em 
            <?= date('d/m/Y H:i', strtotime($noticia['data_post'])) ?></em>
        </p>

        <?php if (!empty($noticia['imagem'])): ?>
            <img src="../src/img/<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem da notícia" style="max-width: 100%; height: auto; margin: 20px 0;">
        <?php endif; ?>

        <div class="conteudo-postagem">
            <?= $noticia['texto'] ?>
        </div>

        <hr>

        <section class="comentarios">
            <h2>Comentários</h2>

            <?php if ($comentarios->num_rows > 0): ?>
                <?php while ($coment = $comentarios->fetch_assoc()): ?>
                    <div class="comentario" style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                        <strong><?= htmlspecialchars($coment['nome']) ?></strong>
                        <small>em <?= date('d/m/Y H:i', strtotime($coment['data_comentario'])) ?></small>
                        <div><?= $coment['comentario'] ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Seja o primeiro a comentar!</p>
            <?php endif; ?>
        </section>

        <hr>

        <section class="formulario-comentario">
            <h3>Deixe um comentário</h3>
            <form action="../comentar.php" method="POST" onsubmit="return enviarComentario();">
                <input type="hidden" name="id_post" value="<?= $id ?>">
                <input type="hidden" name="comentario" id="comentario-hidden">
                <div id="editor" class="quill-editor" style="height: 150px;"></div>
                <button type="submit" style="margin-top: 10px;">Enviar</button>
            </form>
        </section>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow'
        });

        function enviarComentario() {
            const hiddenInput = document.getElementById('comentario-hidden');
            hiddenInput.value = quill.root.innerHTML.trim();
            return hiddenInput.value.length > 0;
        }
    </script>

</body>

</html>
