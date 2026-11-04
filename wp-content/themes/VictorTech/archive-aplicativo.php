<?php
/**
 * Catálogo público de aplicativos da Fastnet.
 */

get_header();
?>

<main id="conteudo-principal" class="pagina-aplicativo-fastnet">
    <header class="cabecalho-secao">
        <h1 class="titulo-secao">Aplicativos disponíveis</h1>
        <p class="subtitulo-secao">Conheça os aplicativos e serviços digitais oferecidos pela Fastnet.</p>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="catalogo-aplicativos-fastnet">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php $dados = fastnet_obter_dados_do_conteudo( get_the_ID() ); ?>
                <article <?php post_class( 'cartao-aplicativo-fastnet' ); ?>>
                    <a class="cartao-aplicativo-fastnet-imagem" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Conhecer ' . get_the_title() ); ?>">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?>
                        <?php else : ?>
                            <span><?php echo esc_html( get_the_title() ); ?></span>
                        <?php endif; ?>
                    </a>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php if ( ! empty( $dados['categoria_app'] ) ) : ?>
                        <p><strong><?php echo esc_html( $dados['categoria_app'] ); ?></strong></p>
                    <?php endif; ?>
                    <p><?php echo esc_html( ! empty( $dados['descricao_curta'] ) ? $dados['descricao_curta'] : get_the_excerpt() ); ?></p>
                    <a href="<?php the_permalink(); ?>">Ver detalhes</a>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( array( 'prev_text' => 'Anterior', 'next_text' => 'Próxima página' ) ); ?>
    <?php else : ?>
        <p class="aviso-painel-vazio">Ainda não há aplicativos cadastrados.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
