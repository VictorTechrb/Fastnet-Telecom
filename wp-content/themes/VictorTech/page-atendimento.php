<?php
/**
 * Modelo de página: Atendimento Fastnet Telecom.
 *
 * Conteúdo útil para clientes que procuram suporte de internet fibra óptica
 * e informações sobre os serviços de telecomunicações da Fastnet em Sergipe.
 *
 * @package VictorTech
 */

get_header();
?>

<main class="pagina-atendimento-fastnet" id="conteudo-principal">
    <section class="pagina-atendimento-fastnet__apresentacao" aria-labelledby="titulo-atendimento-fastnet">
        <p class="pagina-atendimento-fastnet__etiqueta">Central de atendimento Fastnet</p>
        <h1 id="titulo-atendimento-fastnet">Atendimento Fastnet Telecom em Sergipe</h1>
        <p class="pagina-atendimento-fastnet__descricao">
            Precisa de ajuda com sua internet fibra óptica? Entre em contato com a equipe da Fastnet Telecom.
            Consulte orientações sobre conexão, suporte técnico, planos de internet residencial e empresarial
            e outros serviços de telecomunicações. A disponibilidade dos serviços pode variar conforme a localidade.
        </p>
        <a class="pagina-atendimento-fastnet__botao" href="#assuntos-atendimento">
            Consultar opções de atendimento
        </a>
    </section>

    <section class="pagina-atendimento-fastnet__assuntos" id="assuntos-atendimento" aria-labelledby="titulo-assuntos-atendimento">
        <h2 id="titulo-assuntos-atendimento">Como a Fastnet pode ajudar?</h2>

        <div class="pagina-atendimento-fastnet__grade">
            <article class="pagina-atendimento-fastnet__cartao">
                <span class="pagina-atendimento-fastnet__icone" aria-hidden="true">⌁</span>
                <h3>Suporte para internet fibra óptica</h3>
                <p>Orientações sobre conexão, sinal de internet, rede Wi-Fi e funcionamento dos equipamentos.</p>
            </article>

            <article class="pagina-atendimento-fastnet__cartao">
                <span class="pagina-atendimento-fastnet__icone" aria-hidden="true">＋</span>
                <h3>Planos de internet residencial e empresarial</h3>
                <p>Conheça as opções de internet da Fastnet e esclareça dúvidas antes de contratar.</p>
                <a href="<?php echo esc_url(home_url('/#planos')); ?>">Consultar planos de internet</a>
            </article>

            <article class="pagina-atendimento-fastnet__cartao">
                <span class="pagina-atendimento-fastnet__icone" aria-hidden="true">☏</span>
                <h3>Atendimento ao cliente Fastnet</h3>
                <p>Converse com a equipe sobre os serviços de telecomunicações e o atendimento ao assinante.</p>
                <a href="<?php echo esc_url(home_url('/contato/')); ?>">Ver canais de contato</a>
            </article>
        </div>

        <p class="pagina-atendimento-fastnet__orientacao">
            Para iniciar uma conversa online, selecione o botão
            <strong>Fale com a Fastnet</strong>, no canto inferior da tela.
        </p>
    </section>
</main>

<?php get_footer(); ?>
