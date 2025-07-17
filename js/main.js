// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    // Hero Slider
    const boxes = document.querySelectorAll('.box-content');
    const buttons = document.querySelectorAll('.circular-button');
    let currentIndex = 0;
    
    function showSlide(index) {
        boxes.forEach((box, i) => {
            box.classList.toggle('active', i === index);
        });
        buttons.forEach((button, i) => {
            button.classList.toggle('active', i === index);
        });
    }
    
    function nextSlide() {
        currentIndex = (currentIndex + 1) % boxes.length;
        showSlide(currentIndex);
    }
    
    // Auto-advance every 3 seconds
    setInterval(nextSlide, 3000);
    
    // Manual navigation
    buttons.forEach((button, index) => {
        button.addEventListener('click', () => {
            currentIndex = index;
            showSlide(currentIndex);
        });
    });

    // Services Section Interaction
    document.querySelectorAll('.services-box').forEach(box => {
        box.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetElement = document.getElementById(targetId);
            
            document.querySelectorAll('.services-links-display').forEach(el => {
                el.style.display = 'none';
            });
            
            targetElement.style.display = 'block';
        });
    });
});