<script>document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle ---
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }

    // --- Tabbed Navigation for Health Events Calendar (existing logic) ---
    const eventTabButtons = document.querySelectorAll('.tabs-container .tab-button');
    const eventTabContents = document.querySelectorAll('.event-details-tab-content');

    if (eventTabButtons.length > 0 && eventTabContents.length > 0) {
        eventTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                eventTabButtons.forEach(btn => btn.classList.remove('active'));
                eventTabContents.forEach(content => content.classList.add('hidden'));

                this.classList.add('active');

                const targetTabId = this.dataset.tab;
                const targetContent = document.getElementById(targetTabId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });

        const initialActiveEventTab = document.querySelector('.tab-button.active');
        if (initialActiveEventTab) {
            const initialTargetId = initialActiveEventTab.dataset.tab;
            const initialContent = document.getElementById(initialTargetId);
            if (initialContent) {
                initialContent.classList.remove('hidden');
            }
        }
    }

    // --- Tabbed Navigation for Podcast Series (NEW logic) ---
    const podcastTabButtons = document.querySelectorAll('.podcast-tabs-container .podcast-tab-button');
    const podcastTabContents = document.querySelectorAll('.podcast-tab-content');

    if (podcastTabButtons.length > 0 && podcastTabContents.length > 0) {
        podcastTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove 'active' class from all podcast buttons
                podcastTabButtons.forEach(btn => btn.classList.remove('active'));
                // Hide all podcast tab contents
                podcastTabContents.forEach(content => content.classList.add('hidden'));

                // Add 'active' class to the clicked podcast button
                this.classList.add('active');

                // Show the corresponding podcast content
                const targetTabId = this.dataset.tab; // Get the data-tab attribute value
                const targetContent = document.getElementById(targetTabId);
                if (targetContent) {
                    targetContent.classList.remove('hidden'); // Show the target content
                }
            });
        });

        // Ensure the first podcast tab is active and its content is shown on page load
        const initialActivePodcastTab = document.querySelector('.podcast-tab-button.active');
        if (initialActivePodcastTab) {
            const initialTargetId = initialActivePodcastTab.dataset.tab;
            const initialContent = document.getElementById(initialTargetId);
            if (initialContent) {
                initialContent.classList.remove('hidden');
            }
        }
    }
});
</script>