window.addEventListener('scroll', function() {
    let hero = document.getElementById('hero');
    let scrollPosition = window.scrollY;
    hero.style.backgroundPositionY = -scrollPosition * 0.2 + 'px'; 
});