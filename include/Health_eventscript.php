<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle ---
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }

    // --- Tabbed Navigation for Health Events Calendar ---
    const tabButtons = document.querySelectorAll('.tabs-container .tab-button');
    const tabContents = document.querySelectorAll('.event-details-tab-content');

    if (tabButtons.length > 0 && tabContents.length > 0) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove 'active' class from all buttons and 'show' from all contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.add('hidden')); // Hide all contents

                // Add 'active' class to the clicked button
                this.classList.add('active');

                // Show the corresponding content
                const targetTabId = this.dataset.tab; // Get the data-tab attribute value
                const targetContent = document.getElementById(targetTabId);
                if (targetContent) {
                    targetContent.classList.remove('hidden'); // Show the target content
                }
            });
        });

        // Optional: Ensure the first tab is active and its content is shown on page load
        // This is already handled by the 'active' class in HTML, but good for robustness.
        const initialActiveTab = document.querySelector('.tab-button.active');
        if (initialActiveTab) {
            const initialTargetId = initialActiveTab.dataset.tab;
            const initialContent = document.getElementById(initialTargetId);
            if (initialContent) {
                initialContent.classList.remove('hidden');
            }
        }
    }
});
</script>