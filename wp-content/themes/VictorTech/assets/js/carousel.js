(function () {
    'use strict';

    document.querySelectorAll('[data-carousel]').forEach(function (carousel) {
        var track = carousel.querySelector('[data-carousel-track]');
        var slides = carousel.querySelectorAll('[data-carousel-slide]');
        var previous = carousel.querySelector('[data-carousel-prev]');
        var next = carousel.querySelector('[data-carousel-next]');
        var dots = carousel.querySelector('[data-carousel-dots]');
        var current = 0;
        var timer;

        if (!track || slides.length < 2) {
            return;
        }

        function showSlide(index) {
            current = (index + slides.length) % slides.length;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
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
            timer = window.setInterval(function () {
                showSlide(current + 1);
            }, 6000);
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
        restart();
    });
}());
