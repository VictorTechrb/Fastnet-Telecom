<?php
/**
 * Template Name: Atendimento Fastnet
 * Template Post Type: page
 *
 * @package VictorTech
 */

get_header();
?>

<main class="pagina-atendimento" id="conteudo-principal">
    <section class="pagina-atendimento__area" aria-label="Conversa com a Fastnet">
        <?php get_template_part( 'template-parts/chat/widget' ); ?>
    </section>
</main>

<?php get_footer(); ?>
