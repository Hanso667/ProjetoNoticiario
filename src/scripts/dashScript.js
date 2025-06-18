let cards = document.querySelectorAll('.card-noticias');


cards.forEach(card => {
    card.addEventListener("click", function (e) {
         let cardId = card.getAttribute("data-id");
        window.location.href = "../pages/noticia.php?id=" + cardId;
    })
});