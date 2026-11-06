(function () {
    'use strict';

    var filtros = document.querySelectorAll('[data-filtro-aplicativo]');
    var cartoes = document.querySelectorAll('[data-categoria-aplicativo]');

    if (!filtros.length || !cartoes.length) {
        return;
    }

    filtros.forEach(function (filtro) {
        filtro.addEventListener('click', function () {
            var categoria = filtro.getAttribute('data-filtro-aplicativo') || 'todos';

            filtros.forEach(function (outroFiltro) {
                var ativo = outroFiltro === filtro;
                outroFiltro.classList.toggle('is-active', ativo);
                outroFiltro.setAttribute('aria-pressed', ativo ? 'true' : 'false');
            });

            cartoes.forEach(function (cartao) {
                var visivel = 'todos' === categoria || cartao.getAttribute('data-categoria-aplicativo') === categoria;
                cartao.hidden = !visivel;
            });
        });
    });
}());
