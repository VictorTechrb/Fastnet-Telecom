(function () {
    'use strict';

    var button = document.querySelector('.botao-menu-mobile');
    var menu = document.querySelector('.navegacao-fastnet');

    if (!button || !menu) {
        return;
    }

    button.addEventListener('click', function () {
        var isOpen = menu.classList.toggle('is-open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
}());
