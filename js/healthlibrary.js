document.addEventListener('DOMContentLoaded', function() {
    // Highlight current page in navigation
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.parentElement.classList.add('active');
        }
    });
    
    // Cookie notice functionality
    const cookieButton = document.querySelector('.cookie-button');
    if (cookieButton) {
        cookieButton.addEventListener('click', function() {
            // In a real implementation, this would set a cookie preference
            alert('Cookie preferences would be shown here in a production environment.');
        });
    }
    
    // Mobile menu toggle
    const hamburger = document.querySelector('.hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }    
});