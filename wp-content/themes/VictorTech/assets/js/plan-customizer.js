(function () {
    'use strict';

    document.querySelectorAll('[data-plan-card]').forEach(function (cartao) {
        var precoBase = Number(cartao.getAttribute('data-plan-price')) || 0;
        var total = cartao.querySelector('[data-plan-total]');
        var linkContratacao = cartao.querySelector('[data-plan-contract]');

        if (!total) {
            return;
        }

        function obterAplicativosSelecionados() {
            var selecionados = [];

            cartao.querySelectorAll('[data-option-price].is-selected').forEach(function (opcao) {
                selecionados.push({
                    nome: opcao.getAttribute('data-option-name') || 'Aplicativo',
                    categoria: opcao.getAttribute('data-option-category') || '',
                    preco: Number(opcao.getAttribute('data-option-price')) || 0
                });
            });

            return selecionados;
        }

        function atualizarTotal() {
            var aplicativos = obterAplicativosSelecionados();
            var adicionais = aplicativos.reduce(function (soma, aplicativo) {
                return soma + aplicativo.preco;
            }, 0);

            total.textContent = (precoBase + adicionais).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        cartao.querySelectorAll('[data-plan-tab]').forEach(function (aba) {
            aba.addEventListener('click', function () {
                var destino = aba.getAttribute('data-plan-tab');

                cartao.querySelectorAll('[data-plan-tab]').forEach(function (outraAba) {
                    var ativa = outraAba === aba;
                    outraAba.classList.toggle('is-active', ativa);
                    outraAba.setAttribute('aria-selected', ativa ? 'true' : 'false');
                });

                cartao.querySelectorAll('[data-plan-panel]').forEach(function (painel) {
                    var visivel = painel.getAttribute('data-plan-panel') === destino;
                    painel.classList.toggle('is-active', visivel);
                    painel.hidden = !visivel;
                });
            });
        });

        cartao.querySelectorAll('[data-option-price]').forEach(function (opcao) {
            opcao.addEventListener('click', function () {
                var selecionada = opcao.classList.toggle('is-selected');
                opcao.setAttribute('aria-pressed', selecionada ? 'true' : 'false');
                atualizarTotal();
            });
        });

        if (linkContratacao) {
            linkContratacao.addEventListener('click', function (evento) {
                var endereco = linkContratacao.href;
                var url;

                try {
                    url = new URL(endereco, window.location.href);
                } catch (erro) {
                    return;
                }

                var eWhatsApp = url.hostname === 'wa.me' ||
                    url.hostname === 'api.whatsapp.com' ||
                    url.hostname === 'web.whatsapp.com';

                if (!eWhatsApp) {
                    return;
                }

                evento.preventDefault();

                var nomePlano = cartao.getAttribute('data-plan-name') || 'Plano Fastnet';
                var velocidade = cartao.getAttribute('data-plan-speed') || '';
                var aplicativos = obterAplicativosSelecionados();
                var adicional = aplicativos.reduce(function (soma, aplicativo) {
                    return soma + aplicativo.preco;
                }, 0);
                var totalMensal = precoBase + adicional;
                var linhasAplicativos = aplicativos.length
                    ? aplicativos.map(function (aplicativo) {
                        return '- ' + aplicativo.nome +
                            (aplicativo.categoria ? ' (' + aplicativo.categoria + ')' : '') +
                            ': R$ ' + aplicativo.preco.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                    }).join('\n')
                    : 'Nenhum aplicativo adicional selecionado.';

                var mensagem = [
                    'Olá! Tenho interesse em contratar um plano da Fastnet Telecom.',
                    '',
                    'Plano: ' + nomePlano,
                    velocidade ? 'Velocidade: ' + velocidade + ' Mega' : '',
                    'Valor do plano: R$ ' + precoBase.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }),
                    '',
                    'Aplicativos adicionais:',
                    linhasAplicativos,
                    '',
                    'Valor mensal estimado: R$ ' + totalMensal.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }),
                    '',
                    'Gostaria de mais informações para concluir a contratação.'
                ].filter(function (linha) {
                    return linha !== '';
                }).join('\n');

                var textoAnterior = url.searchParams.get('text');
                if (textoAnterior) {
                    mensagem = textoAnterior + '\n\n' + mensagem;
                }
                url.searchParams.set('text', mensagem);

                window.open(url.toString(), '_blank', 'noopener,noreferrer');
            });
        }

        atualizarTotal();
    });
}());
