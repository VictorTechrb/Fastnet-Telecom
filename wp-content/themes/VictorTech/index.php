<?php
/**
 * Homepage oficial da Fastnet Telecom.
 */

get_header();

$h1_seo_home = fastnet_campo_home( 'home_h1_seo', 'Internet 100% Fibra Óptica de alta velocidade' );
$titulo_planos = fastnet_campo_home( 'home_titulo_planos', 'Internet pensada para a sua rotina' );
$subtitulo_planos = fastnet_campo_home( 'home_subtitulo_planos', 'Planos 100% fibra óptica, estabilidade total e instalação rápida.' );
$configuracoes_home = fastnet_obter_configuracoes_pagina_inicial();
$tem_whatsapp_home = ! empty( $configuracoes_home['whatsapp_url'] );
$link_whatsapp_home = $tem_whatsapp_home ? $configuracoes_home['whatsapp_url'] : '#planos';
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
                                        <span class="selo-fibra-optica">Internet 100% fibra óptica</span>
                                        <h2 class="titulo-velocidade-mega"><?php echo esc_html($banner['titulo']); ?></h2>
                                        <?php if (!empty($banner['subtitulo'])): ?>
                                            <p class="descricao-chamada-fibra"><?php echo esc_html($banner['subtitulo']); ?></p>
                                        <?php endif; ?>
                                        <div class="acoes-hero-fastnet">
                                            <a href="<?php echo esc_url($banner['link'] ? $banner['link'] : '#planos'); ?>"
                                                class="botao-assinar-fibra"><?php echo esc_html( ! empty( $banner['texto_botao'] ) ? $banner['texto_botao'] : 'Conhecer ofertas' ); ?></a>
                                            <span class="nota-hero-fastnet">Instalação rápida e suporte local</span>
                                        </div>
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
                    <button class="alternar-carrossel" type="button" data-carousel-toggle aria-label="Pausar rotação de banners">
                        <span aria-hidden="true">Pausar</span>
                    </button>
                <?php else: ?>
                    <div class="slide-banner-item slide-padrao-telecom">
                        <div class="conteudo-slide">
                            <div class="chamada-plano-mobile">
                                <span class="selo-fibra-optica">Internet 100% fibra óptica</span>
                                <h2 class="titulo-chamada-mobile">Internet que acompanha o seu ritmo.</h2>
                                <p class="descricao-chamada-mobile">Velocidade e estabilidade para trabalhar, jogar e curtir sem interrupções.</p>
                            </div>
                            <div class="card-oferta-padrao">
                                <span class="selo-fibra-optica">Fastnet Telecom</span>
                                <h2 class="titulo-velocidade-mega">Sua vida pede mais conexão.</h2>
                                <p class="descricao-chamada-fibra">Fibra de verdade, baixa latência e uma equipe perto de você para tudo fluir melhor.</p>
                            </div>
                            <div class="acoes-hero-fastnet">
                                <a href="#planos" class="botao-assinar-fibra">Ver planos de internet</a>
                                <span class="nota-hero-fastnet">Consulte disponibilidade na sua região</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="secao-planos-banda-larga" id="planos">
        <div class="container-central">
            <header class="cabecalho-secao">
                <span class="rotulo-secao">Planos residenciais</span>
                <h2 class="titulo-secao"><?php echo esc_html($titulo_planos); ?></h2>
                <p class="subtitulo-secao"><?php echo esc_html($subtitulo_planos); ?></p>
            </header>

            <div class="grade-planos-telecom">
                <?php
                $aplicativos_inclusos = fastnet_obter_aplicativos_inclusos();
                $aplicativos_adicionais = fastnet_obter_aplicativos_adicionais();

                $query_planos = new WP_Query(
                    array(
                        'post_type'      => 'plano',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'orderby'        => 'date',
                        'order'          => 'ASC',
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
                                'aplicativos_adicionais' => $aplicativos_adicionais,
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
    <section class="secao-cobertura-fastnet" aria-labelledby="titulo-cobertura">
        <div class="container-central cobertura-fastnet-layout">
            <div class="cobertura-fastnet-conteudo">
                <span class="rotulo-secao rotulo-secao--claro">Presença regional</span>
                <h2 id="titulo-cobertura">A Fastnet está cada vez mais perto de você</h2>
                <p>Conectamos cidades e comunidades de Sergipe com uma rede de fibra óptica preparada para o presente e para o futuro.</p>
                <a class="botao-cobertura-fastnet" href="<?php echo esc_url( $link_whatsapp_home ); ?>"<?php echo $tem_whatsapp_home ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>Consultar meu endereço</a>
            </div>
            <div class="lista-cidades-fastnet" aria-label="Cidades e regiões atendidas">
                <?php foreach ($cidades_atendidas as $cidade): ?>
                    <span><?php echo esc_html($cidade); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="secao-faq-fastnet" aria-labelledby="titulo-faq">
        <div class="container-central faq-fastnet-layout">
            <header class="cabecalho-secao cabecalho-secao--esquerda">
                <span class="rotulo-secao">Dúvidas frequentes</span>
                <h2 class="titulo-secao" id="titulo-faq">Tudo o que você precisa saber</h2>
                <p class="subtitulo-secao">Respostas rápidas para escolher seu plano com tranquilidade.</p>
            </header>
            <div class="lista-faq-fastnet">
                <details>
                    <summary>A internet é fibra óptica até dentro de casa?</summary>
                    <p>Sim. A tecnologia FTTH leva a fibra até a residência, ajudando a entregar mais estabilidade e velocidade na conexão.</p>
                </details>
                <details>
                    <summary>Como descubro se há cobertura no meu endereço?</summary>
                    <p>Fale com nossa equipe pelo WhatsApp. Consultamos seu endereço e apresentamos os planos disponíveis para a sua região.</p>
                </details>
                <details>
                    <summary>Qual plano é ideal para a minha casa?</summary>
                    <p>Depende da quantidade de pessoas, dispositivos e atividades. Nossa equipe ajuda a escolher a velocidade adequada sem complicação.</p>
                </details>
                <details>
                    <summary>A Fastnet oferece suporte técnico?</summary>
                    <p>Sim. Você conta com atendimento próximo e uma equipe preparada para orientar e resolver problemas de conexão.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="cta-final-fastnet" aria-label="Contrate internet Fastnet">
        <div class="container-central cta-final-fastnet-conteudo">
            <div>
                <span>Pronto para navegar melhor?</span>
                <h2>Leve a velocidade Fastnet para a sua casa.</h2>
            </div>
            <a href="<?php echo esc_url( $link_whatsapp_home ); ?>"<?php echo $tem_whatsapp_home ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>Falar com um Atendente</a>
        </div>
    </section>

</main>


<?php get_footer(); ?>
