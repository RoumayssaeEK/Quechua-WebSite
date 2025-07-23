<footer class="mt-5 py-4">
    <div class="container text-center">
        <p class="mb-2">&copy; 2025 Ma chanson en quechua. Tous droits réservés.</p>
        <p class="mb-0" style="opacity: 0.8;">
            
        </p>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(44, 62, 80, 0.98)';
        } else {
            navbar.style.background = 'rgba(44, 62, 80, 0.95)';
        }
    });


    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                statNumbers.forEach(stat => {
                    const finalNumber = stat.textContent;
                    if (!isNaN(parseInt(finalNumber))) {
                        animateNumber(stat, parseInt(finalNumber));
                    }
                });
            }
        });
    }, observerOptions);

    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        observer.observe(statsSection);
    }

    function animateNumber(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current) + (target >= 1000000 ? 'M' : target >= 1000 ? '+' : '');
        }, 40);
    }
</script>

</body>
</html>