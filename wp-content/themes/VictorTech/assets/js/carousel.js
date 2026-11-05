(function () {
    'use strict';

    document.querySelectorAll('[data-carousel]').forEach(function (carousel) {
        var track = carousel.querySelector('[data-carousel-track]');
        var slides = carousel.querySelectorAll('[data-carousel-slide]');
        var previous = carousel.querySelector('[data-carousel-prev]');
        var next = carousel.querySelector('[data-carousel-next]');
        var dots = carousel.querySelector('[data-carousel-dots]');
        var toggle = carousel.querySelector('[data-carousel-toggle]');
        var current = 0;
        var timer;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var paused = reducedMotion;

        if (!track || slides.length < 2) {
            return;
        }

        track.setAttribute('aria-live', paused ? 'polite' : 'off');

        function showSlide(index) {
            current = (index + slides.length) % slides.length;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            slides.forEach(function (slide, slideIndex) {
                slide.setAttribute('aria-hidden', slideIndex === current ? 'false' : 'true');
            });
            dots.querySelectorAll('button').forEach(function (dot, dotIndex) {
                dot.classList.toggle('is-active', dotIndex === current);
                dot.setAttribute('aria-current', dotIndex === current ? 'true' : 'false');
            });
        }

        slides.forEach(function (_, index) {
            var dot = document.createElement('button');
            dot.className = 'indicador-carrossel' + (index === 0 ? ' is-active' : '');
            dot.type = 'button';
            dot.setAttribute('aria-label', 'Ir para o banner ' + (index + 1));
            dot.addEventListener('click', function () {
                showSlide(index);
                restart();
            });
            dots.appendChild(dot);
        });

        function restart() {
            window.clearInterval(timer);
            if (paused || document.hidden) {
                return;
            }
            timer = window.setInterval(function () {
                showSlide(current + 1);
            }, 6000);
        }

        function setPaused(nextPaused) {
            paused = nextPaused;
            track.setAttribute('aria-live', paused ? 'polite' : 'off');
            if (toggle) {
                toggle.setAttribute('aria-label', paused ? 'Iniciar rotação de banners' : 'Pausar rotação de banners');
                toggle.querySelector('span').textContent = paused ? 'Reproduzir' : 'Pausar';
            }
            restart();
        }

        previous.addEventListener('click', function () {
            showSlide(current - 1);
            restart();
        });
        next.addEventListener('click', function () {
            showSlide(current + 1);
            restart();
        });
        carousel.addEventListener('mouseenter', function () { window.clearInterval(timer); });
        carousel.addEventListener('mouseleave', restart);
        carousel.addEventListener('focusin', function () { window.clearInterval(timer); });
        carousel.addEventListener('focusout', restart);

        if (toggle) {
            toggle.addEventListener('click', function () {
                setPaused(!paused);
            });
        }

        carousel.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft') {
                showSlide(current - 1);
                restart();
            } else if (event.key === 'ArrowRight') {
                showSlide(current + 1);
                restart();
            }
        });

        document.addEventListener('visibilitychange', restart);
        showSlide(0);
        setPaused(paused);
        restart();
    });
}());
