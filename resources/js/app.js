// Animasi muncul saat scroll: masuk layar tampil, keluar layar sembunyi lagi
document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('[data-reveal]');
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!items.length || calm || !('IntersectionObserver' in window)) return;

    document.documentElement.classList.add('reveal-ready');

    const observer = new IntersectionObserver(
        (entries) => entries.forEach((e) => e.target.classList.toggle('is-visible', e.isIntersecting)),
        { threshold: 0.12, rootMargin: '0px 0px -6% 0px' },
    );

    items.forEach((el) => observer.observe(el));
});