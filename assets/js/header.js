(() => {
    const header = document.querySelector('[data-site-header]');
    if (!header) {
        return;
    }

    const root = document.documentElement;

    function updateHeight() {
        root.style.setProperty('--header-height', `${header.offsetHeight}px`);
    }

    function updateScrolled() {
        header.classList.toggle('is-scrolled', window.scrollY > 8);
    }

    updateHeight();
    updateScrolled();

    document.querySelectorAll('[data-back-to-top]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            window.scrollTo({ top: 0, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
        });
    });

    window.addEventListener('resize', updateHeight);
    window.addEventListener('scroll', updateScrolled, { passive: true });
    if ('ResizeObserver' in window) {
        new ResizeObserver(updateHeight).observe(header);
    }
})();
