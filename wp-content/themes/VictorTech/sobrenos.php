<?php
/**
 * Template Name: Sobre Nós
 */

get_header(); 
?>

    <main id="conteudo-principal" class="site-main">
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title" style="color: #fff;">Sobre a Fastnet</h1>
                    <p class="hero-subtitle">Conectando Ribeirópolis e Sergipe com a melhor internet e atendimento de excelência</p>
                </div>
            </div>
        </section>

        <!-- Cards Principais (Empresa, Missão, Visão) -->
        <section class="about-section">
            <div class="container">
                <div class="about-grid">
                    
                    <div class="about-card">
                        <div class="card-icon">
                            <i class="fas fa-building" aria-hidden="true"></i>
                        </div>
                        <h2 class="card-title">Nossa Empresa</h2>
                        <p class="card-text">
                            Desde o início, sempre buscamos evoluir tanto em tecnologia quanto na qualificação da nossa equipe em Ribeirópolis e região. Todas as decisões são tomadas com o foco absoluto em oferecer serviços de excelência e garantir a satisfação total dos nossos clientes.
                        </p>
                    </div>

                    <div class="about-card">
                        <div class="card-icon">
                            <i class="fas fa-bullseye" aria-hidden="true"></i>
                        </div>
                        <h2 class="card-title">Nossa Missão</h2>
                        <p class="card-text">
                            Oferecer serviços e soluções tecnológicas de qualidade em transmissão de dados e internet, impulsionando a produtividade dos nossos clientes com crescimento sustentável e valorização da equipe.
                        </p>
                    </div>

                    <div class="about-card">
                        <div class="card-icon">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </div>
                        <h2 class="card-title">Nossa Visão</h2>
                        <p class="card-text">
                            Ser a provedora referência no mercado de atuação, reconhecida pela segurança, cortesia, eficiência e inovação constante nos serviços prestados.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Seção de Valores -->
        <section class="values-section">
            <div class="container">
                <h2 class="section-title text-center">Nossos Valores</h2>
                <div class="values-grid">
                    <div class="value-item">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <h3>Confiabilidade</h3>
                        <p>Serviços estáveis e seguros para o seu dia a dia.</p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-rocket" aria-hidden="true"></i>
                        <h3>Inovação</h3>
                        <p>Tecnologia de ponta e fibra óptica avançada.</p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        <h3>Atendimento</h3>
                        <p>Suporte especializado, humano e personalizado.</p>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-chart-line" aria-hidden="true"></i>
                        <h3>Crescimento</h3>
                        <p>Expansão contínua para abranger novas localidades.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção de Estatísticas / Números -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Anos de Experiência</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">21+</div>
                        <div class="stat-label">Localidades Atendidas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1GB</div>
                        <div class="stat-label">Velocidade Máxima</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php 
get_footer();