<?php
/**
 * Template Name: Contratos e Segurança
 */

get_header(); ?>

<main class="support-content site-main">
    <!-- Hero Section de Contratos -->
    <section class="support-hero" aria-labelledby="contratos-titulo">
        <div class="container">
            <div class="header-documentacao">
                <div class="header-left">
                    <h1 id="contratos-titulo" style="color: white;"><i class="fas fa-shield-alt" aria-hidden="true"></i> Documentação de Segurança</h1>
                    <p>Acesse documentos importantes sobre segurança digital, contratos e termos de uso.</p>
                </div>
                <div class="header-right">
                    <a href="<?php echo esc_url(
                        get_template_directory_uri() .
                            '/assets/doc/Arquivos_voltados_a_seguranca_unidos.pdf'
                    ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-download-center">
                        <i class="fas fa-download" aria-hidden="true"></i> Central de Downloads
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Secção de Documentos -->
    <div class="container">
        <section id="documents" class="support-section" aria-labelledby="documentos-title">
            <div class="section-header header-documentacao">
                <div class="header-left">
                    <h2 class="section-title" id="documentos-title">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                        Contratos e Termos
                    </h2>
                    <p class="section-description">Visualize e baixe os documentos de referência dos nossos serviços.</p>
                </div>
                <div class="header-right">
                    <a href="<?php echo esc_url(
                        get_template_directory_uri() .
                            '/assets/doc/Arquivos_voltados_a_seguranca_unidos.pdf'
                    ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-download-center">
                        <i class="fas fa-download" aria-hidden="true"></i> Baixar tudo
                    </a>
                </div>
            </div>

            <div class="section-content">
                <div class="documents-grid grid-3x1">
                    
                    <a href="<?php echo esc_url(
                        get_template_directory_uri() .
                            '/assets/doc/CONTRATO-PRESTACAO-DE-SERVICO.pdf'
                    ); ?>" target="_blank" rel="noopener noreferrer" class="document-card">
                        <i class="fas fa-file-contract document-icon" aria-hidden="true"></i>
                        <h3 class="document-title">Prestação de Serviços</h3>
                        <p class="document-description">Termos e condições do serviço de comunicação multimídia.</p>
                    </a>

                    <a href="<?php echo esc_url(
                        get_template_directory_uri() .
                            '/assets/doc/Informacao-e-seguranca.pdf'
                    ); ?>" target="_blank" rel="noopener noreferrer" class="document-card">
                        <i class="fas fa-shield-alt document-icon" aria-hidden="true"></i>
                        <h3 class="document-title">Informação e Segurança</h3>
                        <p class="document-description">Prevenção de ataques, autenticação e boas práticas para manter seus dados seguros.</p>
                    </a>

                    <a href="<?php echo esc_url(
                        get_template_directory_uri() .
                            '/assets/doc/termos_disney.pdf'
                    ); ?>" target="_blank" rel="noopener noreferrer" class="document-card">
                        <i class="fas fa-tv document-icon" aria-hidden="true"></i>
                        <h3 class="document-title">Termos de Uso Disney+</h3>
                        <p class="document-description">Termos e condições para uso do serviço de streaming Disney+.</p>
                    </a>

                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer();
