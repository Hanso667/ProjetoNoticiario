let cards = document.querySelectorAll('.card-noticias');


cards.forEach(card => {
    card.addEventListener("click", function (e) {
        let cardId = card.getAttribute("data-id");
        window.location.href = "../pages/noticia.php?id=" + cardId;
    })
});

var img = document.getElementById('user_picture')
var modal = document.getElementById('Modal');
var modalContent = document.getElementById("modal-content");

img.addEventListener("click", function () {
    console.log("sexo")
    modal.style.display = "flex";
});

window.addEventListener("click", function (event) {
    console.log(event.target)
    if (event.target == modal || event.target == modalContent) {
        modal.style.display = "none";
    }
});