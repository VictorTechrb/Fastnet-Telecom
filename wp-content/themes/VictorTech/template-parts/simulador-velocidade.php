<section class="secao-simulador-inteligente" id="simulador" aria-labelledby="titulo-simulador">
    <div class="container-central">
        
        <!-- Cabeçalho do Simulador -->
        <header class="cabecalho-simulador">
            <span class="etiqueta-simulador">🚀 Simulador Inteligente</span>
            <h2 class="titulo-simulador" id="titulo-simulador">Descubra Sua Velocidade <span class="destaque-gradiente">Ideal</span></h2>
            <p class="subtitulo-simulador">Nossa inteligência artificial analisa seu perfil digital e recomenda a velocidade perfeita para suas necessidades.</p>
        </header>

        <!-- Grade de Conteúdo (Duas Colunas) -->
        <div class="grade-simulador-ia">
            
            <!-- Coluna Esquerda: Checklist de Atividades -->
            <div class="card-painel-simulador">
                <div class="topo-painel-atividades">
                    <h3>📋 Suas Atividades Online</h3>
                    <p>Clique nas atividades que você mais utiliza no dia a dia</p>
                </div>

                <div class="lista-checkbox-atividades">
                    <label class="item-atividade-check" data-peso="50">
                        <input type="checkbox" checked data-atividade="navegacao">
                        <span class="custom-checkbox"></span>
                        <span class="icone-atividade">🌐</span>
                        <span class="nome-atividade">Navegação Básica</span>
                    </label>

                    <label class="item-atividade-check" data-peso="100">
                        <input type="checkbox" checked data-atividade="streaming">
                        <span class="custom-checkbox"></span>
                        <span class="icone-atividade">📺</span>
                        <span class="nome-atividade">Streaming de Vídeo</span>
                    </label>

                    <label class="item-atividade-check" data-peso="150">
                        <input type="checkbox" data-atividade="jogos">
                        <span class="custom-checkbox"></span>
                        <span class="icone-atividade">🎮</span>
                        <span class="nome-atividade">Jogos Online</span>
                    </label>

                    <label class="item-atividade-check" data-peso="100">
                        <input type="checkbox" data-atividade="homeoffice">
                        <span class="custom-checkbox"></span>
                        <span class="icone-atividade">💻</span>
                        <span class="nome-atividade">Videochamadas / Home Office</span>
                    </label>

                    <label class="item-atividade-check" data-peso="200">
                        <input type="checkbox" data-atividade="criacao">
                        <span class="custom-checkbox"></span>
                        <span class="icone-atividade">✨</span>
                        <span class="nome-atividade">Criação de Conteúdo / Live</span>
                    </label>

                    <label class="item-atividade-check" data-peso="80">
                        <input type="checkbox" data-atividade="casa">
                        <span class="custom-checkbox"></span>
                        <span class="icone-atividade">🏠</span>
                        <span class="nome-atividade">Casa Inteligente</span>
                    </label>
                </div>
            </div>

            <!-- Coluna Direita: Resultado Dinâmico & Slider de Dispositivos -->
            <div class="card-painel-simulador painel-resultado-ia">
                
                <!-- Medidor Circular de Velocidade -->
                <div class="medidor-velocidade-ia">
                    <div class="display-mbps">
                        <strong id="velocidade-recomendada-mbps">300</strong>
                        <span class="unidade-mb">MB</span>
                    </div>
                </div>

                <!-- Controle Deslizante (Slider) de Dispositivos Conectados -->
                <div class="controle-dispositivos-bloco">
                    <div class="info-slider-topo">
                        <span class="rotulo-dispositivos">📱 Dispositivos Conectados</span>
                    </div>
                    
                    <input type="range" id="slider-dispositivos" min="1" max="15" value="5" class="range-personalizado" aria-label="Quantidade de dispositivos conectados">
                    
                    <div class="contador-dispositivos-texto">
                        <strong id="contador-dispositivos-valor">5</strong> dispositivo(s)
                    </div>
                </div>

                <!-- Indicador de Plano Sugerido -->
                <div class="badge-plano-sugerido">
                    📊 Plano indicado <strong id="texto-plano-indicado">300MB</strong>
                </div>

                <!-- Botão de Ação CTA -->
                <a href="#planos" class="botao-descobrir-plano-ia" id="botao-acao-simulador">
                    🚀 DESCOBRIR MEU PLANO IDEAL
                </a>

            </div>

        </div>

    </div>
</section>