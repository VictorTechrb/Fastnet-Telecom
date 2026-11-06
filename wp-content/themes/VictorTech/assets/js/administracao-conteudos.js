(function ($) {
    'use strict';

    $(function () {
        $(document).on('click', '.fastnet-selecionar-imagem', function (evento) {
            evento.preventDefault();

            var botao = $(this);
            var grupo = botao.closest('.fastnet-campo-imagem');
            var campo = grupo.find('input[type="hidden"]');
            var previa = grupo.find('.fastnet-previa-imagem');
            var seletor = wp.media({
                title: botao.data('titulo') || 'Selecionar imagem',
                button: { text: 'Usar esta imagem' },
                library: { type: 'image' },
                multiple: false
            });

            seletor.on('select', function () {
                var imagem = seletor.state().get('selection').first().toJSON();
                var endereco = imagem.url;

                if (imagem.sizes && imagem.sizes.medium) {
                    endereco = imagem.sizes.medium.url;
                }

                campo.val(imagem.id);
                previa.empty().append($('<img>', {
                    src: endereco,
                    alt: imagem.alt || ''
                }));
                grupo.find('.fastnet-remover-imagem').prop('hidden', false);
            });

            seletor.open();
        });

        $(document).on('click', '.fastnet-remover-imagem', function (evento) {
            evento.preventDefault();

            var grupo = $(this).closest('.fastnet-campo-imagem');
            grupo.find('input[type="hidden"]').val('');
            grupo.find('.fastnet-previa-imagem').empty();
            $(this).prop('hidden', true);
        });

        $(document).on('click', '.fastnet-adicionar-etapa', function (evento) {
            evento.preventDefault();

            var botao = $(this);
            var container = botao.siblings('[data-etapas-container]');
            var modelo = window.atob(botao.attr('data-etapa-template') || '');
            var indice = container.children('[data-etapa]').length;

            if (!modelo) {
                return;
            }

            container.append(modelo.replace(/__INDICE__/g, String(indice)));
            atualizarNumeracaoEtapas(container);
        });

        $(document).on('click', '.fastnet-remover-etapa', function (evento) {
            evento.preventDefault();
            var container = $(this).closest('[data-etapas-container]');
            $(this).closest('[data-etapa]').remove();
            atualizarNumeracaoEtapas(container);
        });

        function atualizarNumeracaoEtapas(container) {
            container.children('[data-etapa]').each(function (indice) {
                $(this).find('[data-etapa-numero]').text(indice + 1);
            });
        }
    });
}(jQuery));
