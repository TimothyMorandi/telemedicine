<?php
include 'include/head.php';
include 'include/header.php';
?>

    <section
        class="hero-section hero-section-podcast"
        style="background-image: url('images/podcast.jpg');"
    >
        <div class="hero-overlay"></div>
        <div class="hero-content hero-content-small">
            <h1>The Health Wrap Podcast</h1>
        </div>
    </section>

    <section class="podcast-intro-section">
        <div class="container podcast-intro-content">
            <h2 class="section-title">Listen to the Podcast episodes for expert health advice.</h2>
            <p class="podcast-description-text">
                Join us in these podcast episodes as we explore key health topics, allowing you to listen to expert advice on the go.
            </p>
            <div class="podcast-cta-buttons">
                <button class="podcast-cta-button">
                    <svg class="podcast-cta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-5 4v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2a1 1 0 01-1-1z"></path></svg>
                    Never Miss An Episode
                </button>
                <button class="podcast-cta-button">
                    <svg class="podcast-cta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Sign Up to Mediclinic Prime
                </button>
            </div>
        </div>
    </section>

    <section class="podcast-series-section">
        <div class="container">
            <h2 class="section-title">Podcasts</h2>

            <div class="podcast-tabs-container">
                <button class="podcast-tab-button active" data-tab="series-one">Series One: Families with young kids</button>
                <button class="podcast-tab-button" data-tab="series-two">Series Two: On the move</button>
                <button class="podcast-tab-button" data-tab="series-three">Series Three: Diabetes</button>
            </div>

            <div id="series-one" class="podcast-tab-content active">
                <div class="podcast-series-image-container">
                    <img src="https://placehold.co/800x400/E0F2F7/000000?text=Woman+with+Headphones" alt="Podcast Series Image" class="podcast-series-image">
                </div>
                <div class="podcast-cookie-grid">
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>

            <div id="series-two" class="podcast-tab-content hidden">
                <div class="podcast-series-image-container">
                    <img src="https://placehold.co/800x400/E0F2F7/000000?text=On+the+Move+Podcast" alt="Podcast Series Image" class="podcast-series-image">
                </div>
                <div class="podcast-cookie-grid">
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>

            <div id="series-three" class="podcast-tab-content hidden">
                <div class="podcast-series-image-container">
                    <img src="https://placehold.co/800x400/E0F2F7/000000?text=Diabetes+Podcast" alt="Podcast Series Image" class="podcast-series-image">
                </div>
                <div class="podcast-cookie-grid">
                    <div class="cookie-placeholder podcast-cookie-card">
                        <p>Please accept functional cookies to see this content.</p>
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="platforms-section">
        <div class="container">
            <h2 class="section-title">Available on these platforms</h2>
            <div class="platform-buttons-container">
                <a href="#" class="platform-button">
                    <img src="https://placehold.co/150x50/E0F2F7/000000?text=Spotify+Logo" alt="Listen on Spotify Podcasts" class="platform-logo">
                    <span>Listen on Spotify Podcasts</span>
                </a>
                <a href="#" class="platform-button">
                    <img src="https://placehold.co/150x50/E0F2F7/000000?text=Apple+Logo" alt="Listen on Apple Podcasts" class="platform-logo">
                    <span>Listen on Apple Podcasts</span>
                </a>
            </div>
        </div>
    </section>

    <section class="mediclinic-baby-podcast-section">
        <div class="container">
            <div class="mediclinic-baby-podcast-card">
                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p>Mediclinic Baby on the Health Wrap Podcast</p>
            </div>
        </div>
    </section>

<?php
// Include the footer file
include 'include/footer.php';
include 'include/podcast_script.php';
?>