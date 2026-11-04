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
    });
}(jQuery));
