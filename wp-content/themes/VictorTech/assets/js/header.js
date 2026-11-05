(function () {
    'use strict';

    var button = document.querySelector('.botao-menu-mobile');
    var menu = document.querySelector('.navegacao-fastnet');

    if (!button || !menu) {
        return;
    }

    function setOpen(isOpen) {
        menu.classList.toggle('is-open', isOpen);
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        button.setAttribute('aria-label', isOpen ? 'Fechar menu' : 'Abrir menu');
        document.body.classList.toggle('menu-mobile-aberto', isOpen);
    }

    button.addEventListener('click', function () {
        setOpen(button.getAttribute('aria-expanded') !== 'true');
    });

    menu.addEventListener('click', function (event) {
        if (event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            button.focus();
        }
    });
}());
