<?php
/**
 * Modelo de página: Atendimento Fastnet Telecom.
 *
 * Conteúdo visível para apresentar o suporte da Fastnet e orientar visitantes
 * que procuram internet fibra óptica e atendimento em Sergipe.
 *
 * @package VictorTech
 */

get_header();
?>

<main class="pagina-atendimento-fastnet" id="conteudo-principal">
    <section class="pagina-atendimento-fastnet__apresentacao" aria-labelledby="titulo-atendimento-fastnet">
        <p class="pagina-atendimento-fastnet__etiqueta">Central de relacionamento Fastnet</p>
        <h1 id="titulo-atendimento-fastnet">Atendimento Fastnet Telecom em Sergipe</h1>
        <p class="pagina-atendimento-fastnet__descricao">
            Precisa de ajuda com sua internet fibra óptica? Fale com a equipe da Fastnet Telecom.
            Estamos aqui para orientar sobre conexão, suporte técnico, contratação de planos
            residenciais e empresariais e outros serviços de telecomunicações.
        </p>
        <a class="pagina-atendimento-fastnet__botao" href="#assuntos-atendimento">
            Ver assuntos de atendimento
        </a>
    </section>

    <section class="pagina-atendimento-fastnet__assuntos" id="assuntos-atendimento" aria-labelledby="titulo-assuntos-atendimento">
        <h2 id="titulo-assuntos-atendimento">Como podemos ajudar?</h2>
        <div class="pagina-atendimento-fastnet__grade">
            <article class="pagina-atendimento-fastnet__cartao">
                <span class="pagina-atendimento-fastnet__icone" aria-hidden="true">⌁</span>
                <h3>Suporte para internet fibra óptica</h3>
                <p>Orientações sobre conexão, sinal de internet, Wi-Fi e funcionamento dos equipamentos.</p>
            </article>
            <article class="pagina-atendimento-fastnet__cartao">
                <span class="pagina-atendimento-fastnet__icone" aria-hidden="true">＋</span>
                <h3>Planos de internet residencial e empresarial</h3>
                <p>Informações para conhecer os planos e tirar dúvidas antes de contratar a Fastnet.</p>
            </article>
            <article class="pagina-atendimento-fastnet__cartao">
                <span class="pagina-atendimento-fastnet__icone" aria-hidden="true">☏</span>
                <h3>Atendimento ao cliente Fastnet</h3>
                <p>Ajuda com dúvidas sobre os serviços de telecomunicações e acompanhamento do atendimento.</p>
            </article>
        </div>
        <p class="pagina-atendimento-fastnet__orientacao">
            Para iniciar uma conversa, utilize o botão <strong>Fale com a Fastnet</strong> no canto inferior da tela.
        </p>
    </section>
</main>

<?php get_footer(); ?>
