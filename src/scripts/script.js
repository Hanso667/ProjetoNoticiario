let cards = document.querySelectorAll('.card-noticias');


cards.forEach(card => {
    card.addEventListener("click", function (e) {
         let cardId = card.getAttribute("data-id");
        window.location.href = "./pages/noticia.php?id=" + cardId;
    })
});


let form = document.getElementById("form-search-usuarios");
form.style.display = "none";
let formAll = document.getElementById("form-search-all-usuarios");
formAll.style.display = "none";

let hamButton = document.getElementById("");

