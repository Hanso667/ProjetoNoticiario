
var modal = document.getElementById("myModal");
var timer = document.getElementById("timer");
var time = 5;

window.addEventListener("load", function () {

    modal.style.display = "flex";

    setInterval(() => {
        if (time > 0) {
            time = time - 1;
            timer.textContent = "00:0" + time
        }
    }, 1000);

    setTimeout(() => {
        modal.style.display = "none";
    }, 5100);
});

window.addEventListener("click", function (event) {
    console.log(event.target)
    if (event.target == modal) {
        modal.style.display = "none";
    }
});