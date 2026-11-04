<?php
/**
 * Homepage oficial da Fastnet Telecom.
 */

get_header();

$h1_seo_home = fastnet_campo_home( 'home_h1_seo', 'Internet 100% Fibra Óptica de alta velocidade' );
$titulo_planos = fastnet_campo_home( 'home_titulo_planos', 'Internet pensada para a sua rotina' );
$subtitulo_planos = fastnet_campo_home( 'home_subtitulo_planos', 'Planos 100% fibra óptica, estabilidade total e instalação rápida.' );
?>

<main id="conteudo-principal" class="site-principal-telecom">
    <h1 class="leitor-de-tela-seo"><?php echo esc_html($h1_seo_home); ?></h1>

    <section class="secao-hero-fibra" aria-label="Ofertas em destaque Fastnet Telecom">
        <div class="molde-vitrine-carrossel">
            <div class="container-carrossel" data-carousel>
                <?php
                // Os banners cadastrados no menu Carrossel têm prioridade.
                $banners = fastnet_obter_banners();

                // Mantém os campos ACF antigos como alternativa durante a migração.
                if ( empty( $banners ) && function_exists( 'get_field' ) ) {
                    for ( $numero_banner = 1; $numero_banner <= 4; $numero_banner++ ) {
                        $imagem_banner = get_field( 'carrossel_' . $numero_banner );
                        if ( $imagem_banner ) {
                            $banners[] = array(
                                'imagem'      => $imagem_banner,
                                'titulo'      => get_field( 'carrossel_titulo_' . $numero_banner ),
                                'subtitulo'   => get_field( 'carrossel_subtitulo_' . $numero_banner ),
                                'link'        => get_field( 'carrossel_link_' . $numero_banner ),
                                'texto_botao' => 'Conhecer ofertas',
                            );
                        }
                    }
                }
                ?>

                <?php if ($banners): ?>
                    <div class="lista-slides" data-carousel-track>
                        <?php foreach ($banners as $indice => $banner): ?>
                            <?php
                            $imagem_banner = $banner['imagem'];
                            $url_banner = is_array($imagem_banner) ? ($imagem_banner['url'] ?? '') : $imagem_banner;
                            $id_banner = is_array($imagem_banner) ? ($imagem_banner['ID'] ?? 0) : (int) $imagem_banner;
                            $alt_banner = is_array( $imagem_banner ) && ! empty( $imagem_banner['alt'] )
                                ? $imagem_banner['alt']
                                : ( $id_banner ? get_post_meta( $id_banner, '_wp_attachment_image_alt', true ) : '' );
                            if ( '' === $alt_banner ) {
                                $alt_banner = ! empty( $banner['titulo'] ) ? $banner['titulo'] : 'Oferta de internet Fastnet';
                            }
                            ?>
                            <div class="slide-banner-item" data-carousel-slide>
                                <?php if ($id_banner): ?>
                                    <?php
                                    echo wp_get_attachment_image(
                                        $id_banner,
                                        'full',
                                        false,
                                        array(
                                            'class' => 'imagem-slide-banner',
                                            'alt' => esc_attr($alt_banner),
                                            'loading' => 0 === $indice ? 'eager' : 'lazy',
                                            'fetchpriority' => 0 === $indice ? 'high' : 'auto',
                                        )
                                    );
                                    ?>
                                <?php elseif ($url_banner): ?>
                                    <img class="imagem-slide-banner" src="<?php echo esc_url($url_banner); ?>"
                                        alt="<?php echo esc_attr($alt_banner); ?>">
                                <?php endif; ?>

                                <?php if (!empty($banner['titulo'])): ?>
                                    <div class="conteudo-slide">
                                        <span class="selo-fibra-optica">100% Fibra Óptica</span>
                                        <h2 class="titulo-velocidade-mega"><?php echo esc_html($banner['titulo']); ?></h2>
                                        <?php if (!empty($banner['subtitulo'])): ?>
                                            <p class="descricao-chamada-fibra"><?php echo esc_html($banner['subtitulo']); ?></p>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url($banner['link'] ? $banner['link'] : '#planos'); ?>"
                                            class="botao-assinar-fibra"><?php echo esc_html( ! empty( $banner['texto_botao'] ) ? $banner['texto_botao'] : 'Conhecer ofertas' ); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="controle-carrossel controle-anterior" type="button" data-carousel-prev
                        aria-label="Banner anterior">&#10094;</button>
                    <button class="controle-carrossel controle-proximo" type="button" data-carousel-next
                        aria-label="Próximo banner">&#10095;</button>
                    <div class="indicadores-carrossel" data-carousel-dots aria-label="Navegação de banners"></div>
                <?php else: ?>
                    <div class="slide-banner-item slide-padrao-telecom">
                        <div class="conteudo-slide">
                            <div class="chamada-plano-mobile">
                                <span class="selo-fibra-optica">Internet 100% fibra óptica</span>
                                <h2 class="titulo-chamada-mobile">Nós temos o plano perfeito para você!</h2>
                                <p class="descricao-chamada-mobile">Internet com estabilidade e velocidade para toda a família.</p>
                            </div>
                            <div class="card-oferta-padrao">
                                <span class="selo-fibra-optica">Ultravelocidade Fastnet</span>
                                <h2 class="titulo-velocidade-mega">700 <small>MEGA</small></h2>
                                <p class="descricao-chamada-fibra">Conexão rápida e baixa latência para desfrutar dos seus melhores momentos.</p>
                            </div>
                            <a href="#planos" class="botao-assinar-fibra">Conhecer ofertas</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="secao-planos-banda-larga" id="planos">
        <div class="container-central">
            <header class="cabecalho-secao">
                <h2 class="titulo-secao"><?php echo esc_html($titulo_planos); ?></h2>
                <p class="subtitulo-secao"><?php echo esc_html($subtitulo_planos); ?></p>
            </header>

            <div class="grade-planos-telecom">
                <?php
                $configuracoes_home = fastnet_obter_configuracoes_pagina_inicial();
                $aplicativos_inclusos = fastnet_obter_aplicativos_inclusos();
                $apps_por_categoria = fastnet_obter_aplicativos_por_categoria();

                $query_planos = new WP_Query(
                    array(
                        'post_type'      => 'plano',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
                    )
                );

                if ( $query_planos->have_posts() ) :
                    while ( $query_planos->have_posts() ) :
                        $query_planos->the_post();
                        get_template_part(
                            'template-parts/card',
                            'plano',
                            array(
                                'aplicativos_inclusos' => $aplicativos_inclusos,
                                'apps_por_categoria'   => $apps_por_categoria,
                                'configuracoes'        => $configuracoes_home,
                            )
                        );
                    endwhile;
                    wp_reset_postdata();
                else:
                    ?>
                    <div class="aviso-painel-vazio">
                        <p>Os planos serão exibidos aqui assim que forem cadastrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php get_template_part( 'template-parts/simulador', 'velocidade' ); ?>

    <section class="secao-diferenciais-fibra" aria-label="Vantagens tecnológicas da Fastnet">
        <div class="container-central grade-diferenciais">
            <?php
            $diferenciais = array(
                array('⚡', 'dif_1_titulo', 'Fibra Pura FTTH', 'dif_1_texto', 'Sinal óptico direto até o roteador da sua casa sem perda.'),
                array('📶', 'dif_2_titulo', 'Wi-Fi Dual Band', 'dif_2_texto', 'Cobertura inteligente para múltiplos dispositivos simultâneos.'),
                array('🎮', 'dif_3_titulo', 'Ping estável', 'dif_3_texto', 'Conexão preparada para jogos, vídeos e chamadas sem travamentos.'),
                array('🛠️', 'dif_4_titulo', 'Suporte próximo', 'dif_4_texto', 'Equipe local pronta para ajudar quando você precisar.'),
            );
            foreach ($diferenciais as $diferencial):
                ?>
                <article class="item-diferencial-telecom">
                    <span class="icone-diferencial"><?php echo esc_html($diferencial[0]); ?></span>
                    <div>
                        <h2 class="titulo-diferencial">
                            <?php echo esc_html(fastnet_campo($diferencial[1], $diferencial[2])); ?></h2>
                        <p class="texto-diferencial">
                            <?php echo esc_html(fastnet_campo($diferencial[3], $diferencial[4])); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <?php
    $cidades_atendidas = array(
        'Aleixo',
        'Nossa Senhora Aparecida',
        'Aquidabã',
        'Carmópolis',
        'Capela',
        'Nossa Senhora das Dores',
        'Feira Nova',
        'Frei Paulo',
        'Nossa Senhora da Glória',
        'Jenipapo',
        'Japaratuba',
        'Lagoa da Volta',
        'Monte Alegre',
        'Muribeca',
        'Pedro Alexandre',
        'Porto da Folha',
        'Poço Redondo',
        'Ribeirópolis',
        'Nossa Senhora do Socorro',
        'Santa Rosa',
    );
    ?>
    <section class="leitor-de-tela-seo" aria-label="Cidades atendidas pela Fastnet Telecom">
        <h2>Provedor de internet Fastnet Telecom nas cidades atendidas</h2>
        <p>Internet fibra óptica Fastnet Telecom disponível em:</p>
        <ul>
            <?php foreach ($cidades_atendidas as $cidade): ?>
                <li><?php echo esc_html($cidade); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <script>
    document.querySelectorAll('.faq-pergunta').forEach(item => {
        item.addEventListener('click', () => {
            const parent = item.parentElement;
            parent.classList.toggle('active');
        });
    });
    </script>

</main>


<?php get_footer(); ?>