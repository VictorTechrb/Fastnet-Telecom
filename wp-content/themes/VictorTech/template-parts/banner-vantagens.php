<?php
/**
 * Componente: Banner de Vantagens do Plano (Estilo Big Telecom)
 */

if ( ! function_exists( 'fastnet_campo' ) ) {
    function fastnet_campo( $nome_campo, $padrao = '' ) {
        if ( function_exists( 'get_field' ) ) {
            $valor = get_field( $nome_campo );
            if ( ! empty( $valor ) ) {
                return $valor;
            }
        }
        return get_theme_mod( $nome_campo, $padrao );
    }
}

$etiqueta  = fastnet_campo( 'vantagens_etiqueta', 'VANTAGENS DO SEU PLANO' );
$titulo    = fastnet_campo( 'vantagens_titulo', 'Sua conexão pode ter mais vantagens do que você imagina.' );
$descricao = fastnet_campo( 'vantagens_descricao', 'Além da internet, seu plano pode incluir vantagens e serviços que você pode aproveitar. Fale com a nossa equipe e descubra o que está disponível para você.' );
$botao_txt = fastnet_campo( 'vantagens_botao_texto', 'Consultar Vantagens do Plano' );
$botao_url = fastnet_campo( 'vantagens_botao_link', '#chat' );
$imagem_arr = fastnet_campo( 'vantagens_imagem' );

$imagem_url = is_array( $imagem_arr ) ? ( $imagem_arr['url'] ?? '' ) : ( $imagem_arr ? $imagem_arr : get_template_directory_uri() . '/assets/img/vantagens-default.jpg' );
?>

<section class="secao-banner-vantagens-card">
    <div class="container-central">
        <div class="card-flutuante-vantagens">
            
            <div class="conteudo-vantagens-texto">
                <span class="etiqueta-vantagens-topo"><?php echo esc_html( $etiqueta ); ?></span>
                <h2 class="titulo-vantagens-card"><?php echo esc_html( $titulo ); ?></h2>
                <p class="descricao-vantagens-card"><?php echo esc_html( $descricao ); ?></p>
                
                <?php if ( ! empty( $botao_txt ) ) : ?>
                    <a href="<?php echo esc_url( $botao_url ); ?>" class="botao-vermelho-vantagens">
                        <?php echo esc_html( $botao_txt ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="conteudo-vantagens-midia">
                <div class="moldura-recorte-organico">
                    <?php if ( is_array( $imagem_arr ) && isset( $imagem_arr['ID'] ) ) : ?>
                        <?php echo wp_get_attachment_image( $imagem_arr['ID'], 'large', false, array( 'class' => 'imagem-organica-vantagens' ) ); ?>
                    <?php else : ?>
                        <img src="<?php echo esc_url( $imagem_url ); ?>" alt="Vantagens do Plano Fastnet" class="imagem-organica-vantagens" width="500" height="350" loading="lazy">
                    <?php endif; ?>
                    <div class="onda-decorativa-fundo"></div>
                </div>
            </div>

        </div>
    </div>
</section>