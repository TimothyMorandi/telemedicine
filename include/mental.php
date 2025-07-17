<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const accordionItem = this.closest('.accordion-item');
            const accordionContent = this.nextElementSibling;
            const isActive = accordionItem.classList.contains('active');

            // Close all other active accordions
            document.querySelectorAll('.accordion-item.active').forEach(item => {
                if (item !== accordionItem) {
                    item.classList.remove('active');
                    item.querySelector('.accordion-content').style.maxHeight = 0;
                    item.querySelector('.accordion-content').style.padding = '0 20px'; // Reset padding
                }
            });

            // Toggle current accordion
            if (isActive) {
                accordionItem.classList.remove('active');
                accordionContent.style.maxHeight = 0;
                accordionContent.style.padding = '0 20px'; // Reset padding
            } else {
                accordionItem.classList.add('active');
                // Set max-height to scrollHeight for smooth transition
                accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px';
                accordionContent.style.padding = '20px'; // Set actual padding
            }
        });
    });

    // Sticky Sidebar Navigation and Active Link Highlighting
    const sidebarLinks = document.querySelectorAll('.sidebar-nav nav ul li a');
    const contentSections = document.querySelectorAll('.main-content-body .content-section');
    const sidebarNav = document.querySelector('.sidebar-nav');

    function highlightSidebarLink() {
        let currentActive = '';
        contentSections.forEach(section => {
            const sectionTop = section.offsetTop - sidebarNav.offsetHeight - 50; // Offset for header/padding
            const sectionBottom = sectionTop + section.offsetHeight;
            if (window.scrollY >= sectionTop && window.scrollY < sectionBottom) {
                currentActive = section.id;
            }
        });

        sidebarLinks.forEach(link => {
            link.closest('li').classList.remove('active');
            if (link.getAttribute('href').includes(currentActive)) {
                link.closest('li').classList.add('active');
            }
        });
    }

    // Smooth scroll for sidebar links
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                const offsetTop = targetSection.offsetTop - (sidebarNav.offsetHeight + 20); // Adjust scroll position
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });

                // Update active class immediately on click
                sidebarLinks.forEach(l => l.closest('li').classList.remove('active'));
                this.closest('li').classList.add('active');
            }
        });
    });

    window.addEventListener('scroll', highlightSidebarLink);
    highlightSidebarLink(); // Call on load to set initial active state

    // Back to Top button functionality
    const backToTopButton = document.querySelector('.back-to-top');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) { // Show button after scrolling 300px
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });

    backToTopButton.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Hamburger menu (for mobile responsiveness - you'd likely want a full overlay menu here)
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const mainMenu = document.querySelector('.header-main-nav .main-menu'); // Or a mobile-specific menu

    hamburgerMenu.addEventListener('click', () => {
        // This is a basic toggle, for a full professional site, you'd implement a proper mobile menu overlay
        mainMenu.classList.toggle('active-mobile-menu'); // Add a class to show/hide the menu
        // You'll need CSS to handle the '.active-mobile-menu' styling (e.g., display: flex, flex-direction: column)
        // For now, it just shows/hides the desktop menu in a basic way on click
        if (mainMenu.style.display === 'flex') {
            mainMenu.style.display = 'none';
        } else {
            mainMenu.style.display = 'flex';
            mainMenu.style.flexDirection = 'column'; /* Stack items */
            mainMenu.style.position = 'absolute'; /* Or fixed */
            mainMenu.style.top = '100px'; /* Adjust based on header height */
            mainMenu.style.left = '0';
            mainMenu.style.width = '100%';
            mainMenu.style.backgroundColor = '#fff';
            mainMenu.style.boxShadow = '0 5px 10px rgba(0,0,0,0.1)';
            mainMenu.style.padding = '20px';
            mainMenu.style.zIndex = '999';
        }
    });

    // Close mobile menu if window is resized above mobile breakpoint (basic example)
    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) { // Assuming 900px is your breakpoint
            mainMenu.style.display = ''; // Reset display style
            mainMenu.classList.remove('active-mobile-menu');
        }
    });
});
</script>