let cards = document.querySelectorAll('.card-noticias');


cards.forEach(card => {
    card.addEventListener("click", function (e) {
        let cardId = card.getAttribute("data-id");
        window.location.href = "./pages/noticia.php?id=" + cardId;
    })
});


let style = document.getElementById('style');
let darkbtn = document.getElementById('DarkButton');

darkbtn.addEventListener("click", function () {
    const mode = style.getAttribute("data-mode");

    if (mode === "light") {
        style.href = "src/css/indexdark.css";
        style.setAttribute("data-mode", "dark");
        darkbtn.textContent = "🌕";
    } else {
        style.href = "src/css/index.css";
        style.setAttribute("data-mode", "light");
        darkbtn.textContent = "🌑";
    }
});

