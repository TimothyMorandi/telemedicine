<!-- JavaScript for Dynamic Background Image and Scrolling Box -->
<script>
        // Dynamic Background Image
        const heroSection = document.querySelector('.hero-section');
        const images = [
            '../telemedicine/images/home1.jpg',
            '../telemedicine/images/home2.jpeg',
            '../telemedicine/images/hom4.jpeg',
        ];
        let currentImageIndex = 0;

        function changeBackgroundImage() {
            heroSection.style.backgroundImage = `url('${images[currentImageIndex]}')`;
            currentImageIndex = (currentImageIndex + 1) % images.length;
        }

        // Change image every 5 seconds
        setInterval(changeBackgroundImage, 5000);

        // Set initial background image
        changeBackgroundImage();

        // Hamburger Menu for Small Screens
        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');

            hamburger.addEventListener('click', function () {
                navLinks.classList.toggle('active');
                hamburger.classList.toggle('active');
            });
        });
        //This to make the  advert like boxes

        document.addEventListener("DOMContentLoaded", function () {
            const boxes = document.querySelectorAll(".box-content");
            const buttons = document.querySelectorAll(".circular-button");
            let currentIndex = 0;
        
            function showSlide(index) {
                boxes.forEach((box, i) => {
                    box.classList.remove("active");
                    buttons[i].classList.remove("active");
                    if (i === index) {
                        box.classList.add("active");
                        buttons[i].classList.add("active");
                    }
                });
            }
        
            function nextSlide() {
                currentIndex = (currentIndex + 1) % boxes.length;
                showSlide(currentIndex);
            }
        
            // Auto-slide every 1 second
            setInterval(nextSlide, 3000);
        
            // Click event for manual navigation
            buttons.forEach((button, index) => {
                button.addEventListener("click", () => {
                    currentIndex = index;
                    showSlide(currentIndex);
                });
            });
        });
        
        // Get all the service boxes
const serviceBoxes = document.querySelectorAll('.services-box');

// Add click event listeners to each service box
serviceBoxes.forEach(box => {
    box.addEventListener('click', () => {
        // Remove the 'active' class from all service boxes
        serviceBoxes.forEach(b => b.classList.remove('active'));

        // Add the 'active' class to the clicked service box
        box.classList.add('active');

        // Get the target ID from the data-target attribute
        const targetId = box.getAttribute('data-target');
        // Get the target element
        const targetElement = document.getElementById(targetId);

        // Hide all other services-links-display elements
        document.querySelectorAll('.services-links-display').forEach(link => {
            if (link.id !== targetId) {
                link.style.display = 'none';
            }
        });

        // Toggle the display of the target element
        if (targetElement.style.display === 'none' || targetElement.style.display === '') {
            targetElement.style.display = 'block';
        } else {
            targetElement.style.display = 'none';
        }
    });
});

//Footer script
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.footer-section h3');

    sections.forEach(section => {
        section.addEventListener('click', function() {
            const content = this.nextElementSibling;
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        });
    });
});
    </script>