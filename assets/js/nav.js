(() => {
    const links = [...document.querySelectorAll('.site-header nav ul a[href^="#"]')];
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if (!sections.length) {
        return;
    }

    function setActive(id) {
        links.forEach((link) => {
            const active = link.getAttribute('href') === `#${id}`;
            link.parentElement.classList.toggle('active', active);
            if (active) {
                link.setAttribute('aria-current', 'true');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    const observer = new IntersectionObserver((entries) => {
        entries
            .filter((entry) => entry.isIntersecting)
            .forEach((entry) => setActive(entry.target.id));
    }, { rootMargin: "-45% 0px -50% 0px" });

    sections.forEach((section) => observer.observe(section));
})();
