import AOS from 'aos';

document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 700,
        once: true,
        offset: 60,
        easing: 'ease-out-cubic',
    });
});
