<?php
/**
 * Página individual de um aplicativo.
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $dados = fastnet_obter_dados_do_conteudo( get_the_ID() );
        $preco_adicional = isset( $dados['preco_adicional'] ) ? (float) $dados['preco_adicional'] : 0;
        ?>
        <main id="conteudo-principal" class="pagina-aplicativo-fastnet">
            <article <?php post_class(); ?>>
                <header class="pagina-aplicativo-fastnet-cabecalho">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium', array( 'loading' => 'eager' ) ); ?>
                    <?php endif; ?>
                    <div>
                        <?php if ( ! empty( $dados['categoria_app'] ) ) : ?>
                            <p><?php echo esc_html( $dados['categoria_app'] ); ?></p>
                        <?php endif; ?>
                        <h1><?php the_title(); ?></h1>
                        <?php if ( ! empty( $dados['descricao_curta'] ) ) : ?>
                            <p><?php echo esc_html( $dados['descricao_curta'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="pagina-aplicativo-fastnet-conteudo">
                    <?php the_content(); ?>
                </div>

                <?php if ( $preco_adicional > 0 ) : ?>
                    <section aria-label="Preços adicionais do aplicativo">
                        <h2>Preço mensal adicional</h2>
                        <div class="pagina-aplicativo-fastnet-precos">
                            <span><?php echo esc_html( 'R$ ' . number_format_i18n( $preco_adicional, 2 ) ); ?></span>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="pagina-aplicativo-fastnet-links">
                    <?php if ( ! empty( $dados['link_site'] ) ) : ?>
                        <a href="<?php echo esc_url( $dados['link_site'] ); ?>" target="_blank" rel="noopener noreferrer">Site oficial</a>
                    <?php endif; ?>
                    <?php if ( ! empty( $dados['link_play_store'] ) ) : ?>
                        <a href="<?php echo esc_url( $dados['link_play_store'] ); ?>" target="_blank" rel="noopener noreferrer">Google Play</a>
                    <?php endif; ?>
                    <?php if ( ! empty( $dados['link_app_store'] ) ) : ?>
                        <a href="<?php echo esc_url( $dados['link_app_store'] ); ?>" target="_blank" rel="noopener noreferrer">App Store</a>
                    <?php endif; ?>
                </div>
            </article>
        </main>
        <?php
    endwhile;
endif;

get_footer();
