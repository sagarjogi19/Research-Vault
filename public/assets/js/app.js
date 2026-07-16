document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('[data-nav]');
    const navToggle = document.querySelector('[data-nav-toggle]');
    if (nav && navToggle) {
        navToggle.addEventListener('click', () => nav.classList.toggle('open'));
    }

    const sidebar = document.querySelector('[data-admin-sidebar]');
    const adminToggle = document.querySelector('[data-admin-toggle]');
    if (sidebar && adminToggle) {
        adminToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }

    const isFree = document.getElementById('is_free');
    const price = document.getElementById('price');
    if (isFree && price) {
        const syncPrice = () => {
            const free = isFree.value === '1';
            price.disabled = free;
            if (free) {
                price.value = '0';
            }
        };
        isFree.addEventListener('change', syncPrice);
        syncPrice();
    }

    const reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        reveals.forEach((el) => observer.observe(el));
    } else {
        reveals.forEach((el) => el.classList.add('visible'));
    }

    document.querySelectorAll('.alert').forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    });
});
