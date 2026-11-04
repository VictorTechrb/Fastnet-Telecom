(function () {
    'use strict';

    document.querySelectorAll('[data-plan-card]').forEach(function (card) {
        var precoBase = Number(card.getAttribute('data-plan-price')) || 0;
        var total = card.querySelector('[data-plan-total]');

        if (!total) {
            return;
        }

        function atualizarTotal(adicional) {
            total.textContent = (precoBase + (Number(adicional) || 0)).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        var opcaoInicial = card.querySelector('[data-option-price].is-selected');
        atualizarTotal(opcaoInicial ? opcaoInicial.getAttribute('data-option-price') : 0);

        card.querySelectorAll('[data-plan-tab]').forEach(function (aba) {
            aba.addEventListener('click', function () {
                var destino = aba.getAttribute('data-plan-tab');

                card.querySelectorAll('[data-plan-tab]').forEach(function (outraAba) {
                    var ativa = outraAba === aba;
                    outraAba.classList.toggle('is-active', ativa);
                    outraAba.setAttribute('aria-selected', ativa ? 'true' : 'false');
                });

                card.querySelectorAll('[data-plan-panel]').forEach(function (painel) {
                    var visivel = painel.getAttribute('data-plan-panel') === destino;
                    painel.classList.toggle('is-active', visivel);
                    painel.hidden = !visivel;
                });
            });
        });

        card.querySelectorAll('[data-option-price]').forEach(function (opcao) {
            opcao.addEventListener('click', function () {
                card.querySelectorAll('[data-option-price]').forEach(function (outraOpcao) {
                    outraOpcao.classList.remove('is-selected');
                    outraOpcao.setAttribute('aria-pressed', 'false');
                });

                opcao.classList.add('is-selected');
                opcao.setAttribute('aria-pressed', 'true');
                atualizarTotal(opcao.getAttribute('data-option-price'));
            });
        });
    });
}());
