(function () {
    'use strict';

    /*
     * O Webchat oficial cria o próprio botão com a classe .opa_ico.
     * A identidade visual é aplicada ao elemento existente, sem interferir
     * nas mensagens ou na comunicação com o Opa! Suite.
     */
    var classeIdentidade = 'acionador-webchat-fastnet';
    var botaoPersonalizado = null;

    function aplicarIdentidadeFastnet() {
        var iconeWebchat = document.querySelector('.opa_ico');

        if (!iconeWebchat) {
            return;
        }

        var botaoAtendimento = iconeWebchat.closest('button, [role="button"], a');

        if (!botaoAtendimento) {
            botaoAtendimento = iconeWebchat.parentElement || iconeWebchat;
        }

        if (!botaoAtendimento || botaoAtendimento === document.body) {
            return;
        }

        if (botaoPersonalizado && botaoPersonalizado !== botaoAtendimento) {
            botaoPersonalizado.classList.remove(classeIdentidade);
        }

        botaoAtendimento.classList.add(classeIdentidade);
        botaoAtendimento.setAttribute(
            'aria-label',
            'Abrir atendimento online da Fastnet Telecom em Sergipe'
        );
        botaoAtendimento.setAttribute(
            'title',
            'Atendimento Fastnet Telecom em Sergipe'
        );

        if (iconeWebchat.tagName.toLowerCase() === 'img') {
            iconeWebchat.setAttribute('alt', 'Abrir atendimento Fastnet Telecom');
        }

        botaoPersonalizado = botaoAtendimento;
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
