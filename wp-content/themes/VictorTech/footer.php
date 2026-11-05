<?php
/**
 * Rodapé Fastnet Telecom.
 * Dados de contato reaproveitam as mesmas opções do cabeçalho (Aparência > Personalizar).
 */
$telefone_suporte = get_theme_mod( 'fastnet_telefone_suporte', '(79) 3045-4880' );
$telefone_href    = preg_replace( '/[^0-9+]/', '', $telefone_suporte );
$link_central_ixc = get_theme_mod( 'fastnet_link_central', 'https://ixc.fastnettelecom.com/central_assinante_web/login/' );
$link_velocimetro = get_theme_mod( 'fastnet_link_velocimetro', 'https://speedixc.fastnettelecom.com/' );
$link_whatsapp    = get_theme_mod( 'victortech_central_url', '' );
$texto_copyright  = get_theme_mod( 'victortech_footer_text', '' );

if ( '' === trim( (string) $texto_copyright ) ) {
    $texto_copyright = sprintf( '© %s Fastnet Telecom', wp_date( 'Y' ) );
}

/* EDITE AQUI: redes sociais e páginas internas. */
$link_facebook  = '#';
$link_instagram = '#';
$link_whatsapp  = '' !== trim( (string) $link_whatsapp ) ? $link_whatsapp : '#';



$links_uteis = array(
    array( 'Velocímetro', $link_velocimetro ),
    array( 'Segurança da Informacão', '#' ),
    array( 'Contato', '#' ),
    array( 'Central do Assinante', $link_central_ixc ),
);

$logo_arquivo = get_template_directory() . '/assets/images/fastbranco.webp';
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">

<footer class="rodape-fastnet">
    <div class="rodape-principal">
        <div class="container-central">
            <div class="rodape-grade">

                <!-- Marca -->
                <div class="rodape-marca">
                    <a class="rodape-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php if ( file_exists( $logo_arquivo ) ) : ?>
                            <img
                                src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/fastbranco.webp' ); ?>"
                                alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                                width="190"
                                height="41"
                                loading="lazy"
                            >
                        <?php else : ?>
                            <span class="rodape-logo-texto">FASTNET</span>
                        <?php endif; ?>
                    </a>
                    <p class="rodape-descricao">
                        A Fastnet é o melhor provedor de internet fibra óptica de Sergipe. Conectando você com o que há de melhor em tecnologia e velocidade.
                    </p>
                    <div class="rodape-selos">
                        <span class="rodape-selo">100% Fibra óptica</span>
                        <span class="rodape-selo">Suporte</span>
                    </div>
                </div>

                <!-- Fale conosco -->
                <div class="rodape-coluna">
                    <h2 class="rodape-titulo">Fale conosco</h2>
                    <ul class="rodape-contatos">
                        <li>
                            <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
                            <div>
                                <strong>Central de Atendimento</strong>
                                <a href="tel:<?php echo esc_attr( $telefone_href ); ?>"><?php echo esc_html( $telefone_suporte ); ?></a>
                            </div>
                        </li>
                        <li>
                            <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.15l-.3-.18-3 .78.8-2.9-.2-.31A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.25-.12-1.46-.72-1.69-.8-.22-.08-.39-.12-.55.12-.16.25-.63.8-.78.96-.14.17-.29.18-.53.06a6.7 6.7 0 0 1-1.97-1.22 7.4 7.4 0 0 1-1.36-1.69c-.14-.25-.02-.38.1-.5.11-.11.25-.29.37-.43.12-.15.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.55-1.33-.76-1.82-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.43.06-.65.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.12.16 1.73 2.64 4.2 3.7.59.26 1.05.41 1.4.52.59.19 1.13.16 1.55.1.47-.07 1.46-.6 1.66-1.17.2-.58.2-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg>
                            <div>
                                <strong>WhatsApp</strong>
                                <a href="<?php echo esc_url( $link_whatsapp ); ?>" target="_blank" rel="noopener noreferrer">Atendimento Online</a>
                            </div>
                        </li>
                        <li>
                            <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm.75 5v5.2l4 2.4-.75 1.25-4.75-2.85V7h1.5z"/></svg>
                            <div>
                                <strong>Horário de Atendimento</strong>
                                <span>Segunda a Sábado: 8h às 18h</span>
                            </div>
                        </li>
                        <li>
                            <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm.75 5v5.2l4 2.4-.75 1.25-4.75-2.85V7h1.5z"/></svg>
                            <div>
                                <strong>Horário de Atendimento Virtual</strong>
                                <span>Segunda a Sábado: 8h às 20h</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Links úteis -->
                <div class="rodape-coluna">
                    <h2 class="rodape-titulo">Links úteis</h2>
                    <ul class="rodape-links">
                        <?php foreach ( $links_uteis as $item ) : ?>
                            <li><a href="<?php echo esc_url( $item[1] ); ?>"><?php echo esc_html( $item[0] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Redes sociais -->
            <div class="rodape-social">
                <h2 class="rodape-social-titulo">Conecte-se Conosco</h2>
                <div class="rodape-social-lista">
                    <a class="rodape-social-botao" href="<?php echo esc_url( $link_facebook ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.5 21v-8h2.7l.4-3.2h-3.1V7.8c0-.9.25-1.5 1.55-1.5h1.65V3.45A22 22 0 0 0 14.3 3.3c-2.4 0-4.05 1.47-4.05 4.15v2.35H7.5V13h2.75v8z"/></svg>
                        Facebook
                    </a>
                    <a class="rodape-social-botao" href="<?php echo esc_url( $link_instagram ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9a5.5 5.5 0 0 1-5.5 5.5h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2zm0 1.8A3.7 3.7 0 0 0 3.8 7.5v9a3.7 3.7 0 0 0 3.7 3.7h9a3.7 3.7 0 0 0 3.7-3.7v-9a3.7 3.7 0 0 0-3.7-3.7zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4zm5.3-2.6a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4z"/></svg>
                        Instagram
                    </a>
                    <a class="rodape-social-botao" href="<?php echo esc_url( $link_whatsapp ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="rodape-icone" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.15l-.3-.18-3 .78.8-2.9-.2-.31A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.25-.12-1.46-.72-1.69-.8-.22-.08-.39-.12-.55.12-.16.25-.63.8-.78.96-.14.17-.29.18-.53.06a6.7 6.7 0 0 1-1.97-1.22 7.4 7.4 0 0 1-1.36-1.69c-.14-.25-.02-.38.1-.5.11-.11.25-.29.37-.43.12-.15.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.55-1.33-.76-1.82-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.43.06-.65.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.12.16 1.73 2.64 4.2 3.7.59.26 1.05.41 1.4.52.59.19 1.13.16 1.55.1.47-.07 1.46-.6 1.66-1.17.2-.58.2-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="rodape-base">
        <div class="container-central">
            <p class="rodape-copyright"><?php echo esc_html( $texto_copyright ); ?></p>
            <p class="rodape-slogan">É mais que internet, é <strong>Fastnet</strong>!</p>
        </div>
    </div>
</footer>

<?php
// Link acessível para quem desativou scripts no navegador.
?>
<noscript>
    <p class="atendimento-sem-javascript">
        Para falar com a Fastnet Telecom, acesse a
        <a href="<?php echo esc_url(home_url('/atendimento/')); ?>">página de atendimento em Sergipe</a>.
    </p>
</noscript>

<?php wp_footer(); ?>
</body>
</html>
