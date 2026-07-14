document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a').forEach((link) => {
        if (link.href.startsWith('http')) return;
        link.classList.add('js-link');
    });

    document.querySelectorAll('[data-scroll-target]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            const target = trigger.getAttribute('data-scroll-target');
            const element = document.querySelector(target);
            if (element) {
                event.preventDefault();
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
