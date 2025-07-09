let dButton = document.getElementById('DarkButton')

window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        dButton.innerHTML = '🌕';
    }
});


dButton.addEventListener('click', function () {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    if (dButton.innerHTML == '🌑') {
        dButton.innerHTML = '🌕';
    } else if (dButton.innerHTML == '🌕') {
        dButton.innerHTML = '🌑';
    }
});



