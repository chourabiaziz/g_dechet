	// Auto-remove toasts after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const toasts = document.querySelectorAll('.toast');
        
        toasts.forEach(toast => { // Add auto-remove class for animation
        toast.classList.add('auto-remove');
        
        // Remove toast from DOM after animation
        setTimeout(() => {
        if (toast.parentElement) {
        toast.remove();
        }
        }, 5000);
        
        // Close on click
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', function () {
        toast.classList.add('fade-out');
        setTimeout(() => {
        if (toast.parentElement) {
        toast.remove();
        }
        }, 600);
        });
        });
        });
           
                // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') 
        return;
        
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
        window.scrollTo({
        top: targetElement.offsetTop - 80,
        behavior: 'smooth'
        });
        }
        });
        });
        
        // Header background on scroll
        window.addEventListener('scroll', function () {
        const header = document.querySelector('header');
        const scrollToTop = document.querySelector('.scroll-to-top');
        
        if (window.scrollY > 100) {
        header.style.backgroundColor = 'rgba(26, 26, 46, 0.98)';
        scrollToTop.classList.add('show');
        } else {
        header.style.backgroundColor = 'rgba(26, 26, 46, 0.95)';
        scrollToTop.classList.remove('show');
        }
        });
        
        // Scroll to top functionality
        document.querySelector('.scroll-to-top').addEventListener('click', function () {
        window.scrollTo({top: 0, behavior: 'smooth'});
        });
        
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        
        themeToggle.addEventListener('click', function () {
        document.documentElement.setAttribute('data-theme', document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        
        if (document.documentElement.getAttribute('data-theme') === 'light') {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        } else {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        }
        });
        
        // Animation on scroll
        const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
        if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        }
        });
        }, observerOptions);
        
        // Observe elements for animation
        document.querySelectorAll('.feature-card, .step, .pricing-card, .testimonial-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
        });
          
                // Avec Choices.js pour un select avancé
        document.addEventListener('DOMContentLoaded', function () {
        const countrySelect = document.querySelector('.country-code-select');
        if (countrySelect) {
        const choices = new Choices(countrySelect, {
        searchEnabled: true,
        itemSelectText: 'tunisie',
        placeholder: true,
        shouldSort: false
        });
        }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.querySelector("#phone");
            
            // Vérifier si l'élément existe avant d'initialiser intlTelInput
            if (phoneInput) {
            const iti = window.intlTelInput(phoneInput, {
            initialCountry: "auto",
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
            });
            }
            });