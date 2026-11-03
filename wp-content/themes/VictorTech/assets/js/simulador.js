(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const simulador = document.getElementById('simulador');
        if (!simulador) {
            return;
        }

        const checkboxes = simulador.querySelectorAll('.item-atividade-check input[type="checkbox"]');
        const sliderDispositivos = document.getElementById('slider-dispositivos');
        const contadorDispositivosValor = document.getElementById('contador-dispositivos-valor');
        const velocidadeRecomendadaMbps = document.getElementById('velocidade-recomendada-mbps');
        const textoPlanoIndicado = document.getElementById('texto-plano-indicado');
        const medidor = simulador.querySelector('.medidor-velocidade-ia');

        if (!sliderDispositivos || !contadorDispositivosValor || !velocidadeRecomendadaMbps || !textoPlanoIndicado) {
            return;
        }

        let velocidadeAtual = Number(velocidadeRecomendadaMbps.textContent) || 200;

        function animarNumero(elemento, inicio, fim, duracao) {
            const inicioAnimacao = performance.now();

            function atualizarNumero(agora) {
                const progresso = Math.min((agora - inicioAnimacao) / duracao, 1);
                const suavizado = 1 - Math.pow(1 - progresso, 3);
                elemento.textContent = Math.round(inicio + (fim - inicio) * suavizado);

                if (progresso < 1) {
                    window.requestAnimationFrame(atualizarNumero);
                }
            }

            window.requestAnimationFrame(atualizarNumero);
        }

        function atualizarProgressoSlider() {
            const minimo = Number(sliderDispositivos.min) || 1;
            const maximo = Number(sliderDispositivos.max) || 15;
            const valor = Number(sliderDispositivos.value) || minimo;
            const progresso = ((valor - minimo) / (maximo - minimo)) * 100;
            sliderDispositivos.style.setProperty('--progresso-slider', progresso + '%');
        }

        function calcularVelocidadeIdeal() {
            let pontosBase = 50; // Base inicial

            // Soma os pesos das atividades selecionadas
            checkboxes.forEach(function (box) {
                if (box.checked) {
                    const itemCard = box.closest('.item-atividade-check');
                    if (itemCard) {
                        pontosBase += parseInt(itemCard.getAttribute('data-peso') || '50', 10);
                    }
                }
            });

            // Multiplicador baseado nos dispositivos conectados
            const numDispositivos = parseInt(sliderDispositivos.value, 10) || 1;
            const multiplicadorDispositivos = numDispositivos * 25;

            const totalCalculado = pontosBase + multiplicadorDispositivos;

            // Arredonda para patamares comerciais de velocidade de fibra (ex: 200, 300, 500, 700, 800)
            let velocidadeFinal = 200;
            if (totalCalculado > 200 && totalCalculado <= 350) {
                velocidadeFinal = 300;
            } else if (totalCalculado > 350 && totalCalculado <= 550) {
                velocidadeFinal = 500;
            } else if (totalCalculado > 550 && totalCalculado <= 750) {
                velocidadeFinal = 700;
            } else if (totalCalculado > 750) {
                velocidadeFinal = 800;
            }

            // Atualiza os elementos visuais na tela
            if (velocidadeFinal !== velocidadeAtual) {
                animarNumero(velocidadeRecomendadaMbps, velocidadeAtual, velocidadeFinal, 450);
                velocidadeAtual = velocidadeFinal;
            } else {
                velocidadeRecomendadaMbps.textContent = velocidadeFinal;
            }
            contadorDispositivosValor.textContent = numDispositivos;
            textoPlanoIndicado.textContent = velocidadeFinal + 'MB';
            atualizarProgressoSlider();

            if (medidor) {
                medidor.classList.remove('is-updated');
                window.requestAnimationFrame(function () {
                    medidor.classList.add('is-updated');
                });
            }
        }

        // Eventos de escuta
        checkboxes.forEach(function (box) {
            box.addEventListener('change', calcularVelocidadeIdeal);
        });

        sliderDispositivos.addEventListener('input', calcularVelocidadeIdeal);

        // Executa no carregamento inicial
        calcularVelocidadeIdeal();
        window.setTimeout(function () {
            simulador.classList.add('is-ready');
        }, 60);
    });
}());