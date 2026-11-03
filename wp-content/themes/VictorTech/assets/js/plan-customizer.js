(function () {
    'use strict';

    document.querySelectorAll('[data-plan-card]').forEach(function (card) {
        var basePrice = Number(card.getAttribute('data-plan-price')) || 0;
        var total = card.querySelector('[data-plan-total]');
        var selectedOption = card.querySelector('[data-option-price].is-selected');

        if (selectedOption && total) {
            total.textContent = (basePrice + Number(selectedOption.getAttribute('data-option-price'))).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        card.querySelectorAll('[data-plan-tab]').forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = tab.getAttribute('data-plan-tab');
                card.querySelectorAll('[data-plan-tab]').forEach(function (item) {
                    var active = item === tab;
                    item.classList.toggle('is-active', active);
                    item.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                card.querySelectorAll('[data-plan-panel]').forEach(function (panel) {
                    var visible = panel.getAttribute('data-plan-panel') === target;
                    panel.classList.toggle('is-active', visible);
                    panel.hidden = !visible;
                });
            });
        });

        card.querySelectorAll('[data-option-price]').forEach(function (option) {
            option.addEventListener('click', function () {
                card.querySelectorAll('[data-option-price]').forEach(function (item) {
                    item.classList.remove('is-selected');
                });
                option.classList.add('is-selected');
                total.textContent = (basePrice + Number(option.getAttribute('data-option-price'))).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            });
        });
    });
}());
