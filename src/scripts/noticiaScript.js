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