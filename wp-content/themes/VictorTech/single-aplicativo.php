<?php
/**
 * Página individual de um aplicativo.
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $dados           = fastnet_obter_dados_do_conteudo( get_the_ID() );
        $preco_adicional = isset( $dados['preco_adicional'] ) ? (float) $dados['preco_adicional'] : 0;
        $imagem_id       = ! empty( $dados['imagem_id'] ) ? absint( $dados['imagem_id'] ) : get_post_thumbnail_id();
        $etapas          = isset( $dados['etapas'] ) && is_array( $dados['etapas'] ) ? $dados['etapas'] : array();
        ?>
        <main id="conteudo-principal" class="pagina-aplicativo-fastnet">
            <article <?php post_class(); ?>>
                <header class="pagina-aplicativo-fastnet-cabecalho">
                    <?php if ( $imagem_id ) : ?>
                        <?php echo wp_get_attachment_image( $imagem_id, 'medium', false, array( 'alt' => get_the_title(), 'loading' => 'eager' ) ); ?>
                    <?php elseif ( has_post_thumbnail() ) : ?>
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

                <?php if ( $etapas ) : ?>
                    <section class="tutorial-aplicativo-fastnet" aria-label="Tutorial de ativação">
                        <h2><?php echo esc_html( ! empty( $dados['titulo_tutorial'] ) ? $dados['titulo_tutorial'] : 'Como ativar ' . get_the_title() ); ?></h2>
                        <div class="etapas-aplicativo-fastnet">
                            <?php foreach ( $etapas as $indice => $etapa ) : ?>
                                <?php if ( ! is_array( $etapa ) ) { continue; } ?>
                                <article class="etapa-aplicativo-fastnet">
                                    <span class="etapa-aplicativo-fastnet-numero"><?php echo esc_html( $indice + 1 ); ?></span>
                                    <div class="etapa-aplicativo-fastnet-conteudo">
                                        <?php if ( ! empty( $etapa['imagem_id'] ) ) : ?>
                                            <?php echo wp_get_attachment_image( absint( $etapa['imagem_id'] ), 'medium', false, array( 'alt' => ! empty( $etapa['titulo'] ) ? $etapa['titulo'] : '', 'loading' => 'lazy' ) ); ?>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $etapa['titulo'] ) ) : ?>
                                            <h3><?php echo esc_html( $etapa['titulo'] ); ?></h3>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $etapa['descricao'] ) ) : ?>
                                            <p><?php echo nl2br( esc_html( $etapa['descricao'] ) ); ?></p>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $etapa['link'] ) ) : ?>
                                            <a href="<?php echo esc_url( $etapa['link'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( ! empty( $etapa['link_texto'] ) ? $etapa['link_texto'] : 'Acessar link' ); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
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
