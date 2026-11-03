(function () {
    'use strict';

    /*
     * O Webchat oficial cria seu próprio botão com a classe .opa_ico.
     * A personalização é aplicada ao elemento existente, sem interceptar
     * mensagens, sessões ou a comunicação com o Opa! Suite.
     */
    var classeIdentidade = 'acionador-webchat-fastnet';
    var acionadorPersonalizado = null;

    function aplicarIdentidadeFastnet() {
        var iconeWebchat = document.querySelector('.opa_ico');

        if (!iconeWebchat) {
            return;
        }

        var acionadorWebchat = iconeWebchat.closest('button, [role="button"], a');

        if (!acionadorWebchat) {
            acionadorWebchat = iconeWebchat.parentElement || iconeWebchat;
        }

        if (!acionadorWebchat || acionadorWebchat === document.body) {
            return;
        }

        if (acionadorPersonalizado && acionadorPersonalizado !== acionadorWebchat) {
            acionadorPersonalizado.classList.remove(classeIdentidade);
        }

        acionadorWebchat.classList.add(classeIdentidade);
        acionadorWebchat.setAttribute(
            'aria-label',
            'Abrir atendimento online da Fastnet Telecom em Sergipe'
        );
        acionadorWebchat.setAttribute(
            'title',
            'Atendimento Fastnet Telecom em Sergipe'
        );

        if (iconeWebchat.tagName.toLowerCase() === 'img') {
            iconeWebchat.setAttribute('alt', 'Abrir atendimento Fastnet Telecom');
        }

        acionadorPersonalizado = acionadorWebchat;
    }

    function acompanharWebchat() {
        aplicarIdentidadeFastnet();

        if (!document.documentElement || !('MutationObserver' in window)) {
            return;
        }

        var observadorWebchat = new MutationObserver(function (alteracoes) {
            for (var indice = 0; indice < alteracoes.length; indice += 1) {
                if (alteracoes[indice].addedNodes.length > 0) {
                    aplicarIdentidadeFastnet();
                    break;
                }
            }
        });

        observadorWebchat.observe(document.documentElement, {
            childList: true,
            subtree: true
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', acompanharWebchat, { once: true });
    } else {
        acompanharWebchat();
    }
})();
