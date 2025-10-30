    window.addEventListener('load', () => {
        const loader = document.getElementById('loading-screen');

        // تأثير fade-out ناعم
        loader.style.transition = 'opacity 0.s ease';
        loader.style.opacity = '0';

        // بعد fade-out، نخفي div نهائياً
        setTimeout(() => {
            loader.style.display = 'none';
        }, 500); // نفس مدة transition
    });

