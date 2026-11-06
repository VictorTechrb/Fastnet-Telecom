<?php
/**
 * Catálogo público de aplicativos da Fastnet.
 */

get_header();
?>

<main id="conteudo-principal" class="pagina-aplicativo-fastnet">
    <header class="cabecalho-secao">
        <h1 class="titulo-secao">Aplicativos do seu plano</h1>
        <p class="subtitulo-secao">Escolha os serviços digitais que combinam com a sua rotina.</p>
    </header>

    <?php if ( have_posts() ) : ?>
        <nav class="filtros-aplicativos-fastnet" aria-label="Filtrar aplicativos">
            <?php
            $categorias = array(
                'todos'        => 'Todos',
                'streaming'    => 'Streaming',
                'musica'       => 'Música',
                'seguranca'    => 'Segurança',
                'jogos-e-rede' => 'Jogos/Rede',
                'outros'       => 'Outros',
            );
            ?>
            <?php foreach ( $categorias as $slug => $nome ) : ?>
                <button type="button" class="filtro-aplicativo-fastnet<?php echo 'todos' === $slug ? ' is-active' : ''; ?>" data-filtro-aplicativo="<?php echo esc_attr( $slug ); ?>" aria-pressed="<?php echo 'todos' === $slug ? 'true' : 'false'; ?>"><?php echo esc_html( $nome ); ?></button>
            <?php endforeach; ?>
        </nav>
        <div class="catalogo-aplicativos-fastnet">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php
                $dados     = fastnet_obter_dados_do_conteudo( get_the_ID() );
                $categoria = isset( $dados['categoria_app'] ) ? $dados['categoria_app'] : '';
                $categoria_slug = sanitize_title( $categoria );
                ?>
                <article <?php post_class( 'cartao-aplicativo-fastnet' ); ?> data-categoria-aplicativo="<?php echo esc_attr( $categoria_slug ); ?>">
                    <a class="cartao-aplicativo-fastnet-imagem" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Conhecer ' . get_the_title() ); ?>">
                        <?php if ( ! empty( $dados['imagem_id'] ) ) : ?>
                            <?php echo wp_get_attachment_image( absint( $dados['imagem_id'] ), 'medium', false, array( 'alt' => get_the_title(), 'loading' => 'lazy' ) ); ?>
                        <?php elseif ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?>
                        <?php else : ?>
                            <span><?php echo esc_html( get_the_title() ); ?></span>
                        <?php endif; ?>
                    </a>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php if ( $categoria ) : ?>
                        <p class="cartao-aplicativo-fastnet-categoria"><strong><?php echo esc_html( 'jogos-e-rede' === $categoria_slug ? 'Jogos/Rede' : $categoria ); ?></strong></p>
                    <?php endif; ?>
                    <p><?php echo esc_html( ! empty( $dados['descricao_curta'] ) ? $dados['descricao_curta'] : get_the_excerpt() ); ?></p>
                    <a href="<?php the_permalink(); ?>">Ver mais</a>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( array( 'prev_text' => 'Anterior', 'next_text' => 'Próxima página' ) ); ?>
    <?php else : ?>
        <p class="aviso-painel-vazio">Ainda não há aplicativos cadastrados.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
