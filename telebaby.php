<?php include 'include/head.php'; ?>
<?php include 'include/header.php'; ?>

<main>
    <section class="hero-pregnancy" style="background-image: linear-gradient(rgba(255, 240, 245, 0.9), rgba(240, 248, 255, 0.9)), url('images/pregnancy-bg.jpg')">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Complete Pregnancy & Baby Care</h1>
                <p>Expert telemedicine support from conception to postpartum care</p>
                <div class="hero-cta">
                    <a href="createaccount.php" class="btn btn-pink">Book Prenatal Consultation</a>
                    <a href="#emergency" class="btn btn-white">24/7 Emergency Line</a>
                </div>
            </div>
        </div>
    </section>
    <section class="breadcrumbs">
            <div class="container">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Telemedicine Mental Health</span>
            </div>
        </section>
    <nav class="pregnancy-nav" aria-label="Main pregnancy navigation">
        <div class="container">
            <a href="#timeline"><i class="fas fa-baby-carriage"></i> Pregnancy Timeline</a>
            <a href="#services"><i class="fas fa-hand-holding-medical"></i> Services</a>
            <a href="#experts"><i class="fas fa-user-md"></i> Specialists</a>
            <a href="#resources"><i class="fas fa-book-medical"></i> Resources</a>
        </div>
        
    </nav>

    <section class="pregnancy-content">
        <div class="container content-grid">
            <aside class="pregnancy-sidebar">
                <div class="calculator-box">
                    <h3><i class="fas fa-calculator"></i> Due Date Calculator</h3>
                    <input type="date" id="last-period" class="calc-input">
                    <button class="btn btn-calculate">Calculate Due Date</button>
                    <div class="result-box"></div>
                </div>
                    <!-- Trimester Content Container -->
                    <div class="trimester-content-container">
                    <a href="#first-trimester" class="nav-item active" data-trimester="1">
                        <i class="fas fa-seedling"></i>
                        <span>First Trimester</span>
                    </a>
                    <a href="#second-trimester" class="nav-item" data-trimester="2">
                        <i class="fas fa-leaf"></i>
                        <span>Second Trimester</span>
                    </a>
                    <a href="#third-trimester" class="nav-item" data-trimester="3">
                        <i class="fas fa-tree"></i>
                        <span>Third Trimester</span>
                    </a>
                    <a href="#postnatal" class="nav-item" data-trimester="4">
                        <i class="fas fa-baby"></i>
                        <span>Postnatal Care</span>
                    </a>
                </div>
                <div class="trimester-content">
                    <!-- Dynamic content will be loaded here -->
                    
            </aside>

            <div class="main-content">
 <!-- Trimester Content Container -->
 <div class="trimester-content">
            <!-- Content will be loaded here -->
        </div>
                <!-- Pregnancy Timeline Section -->
                <section id="timeline" class="content-section">
                    <h2 class="section-title">Pregnancy Journey Timeline</h2>
                    <div class="timeline-container">
                        <div class="timeline-bar"></div>
                        <div class="timeline-steps">
                            <div class="timeline-step" data-week="4">
                                <div class="step-marker"></div>
                                <div class="step-content">
                                    <h4>Confirmation & Early Care</h4>
                                    <p>Pregnancy confirmation and initial consultations</p>
                                </div>
                            </div>
                            <!-- Add more timeline steps -->
                        </div>
                    </div>
                </section>

                <!-- Services Section -->
                <section id="services" class="content-section">
                    <h2 class="section-title">Our Pregnancy Services</h2>
                    <div class="service-filters">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="prenatal">Prenatal</button>
                        <button class="filter-btn" data-filter="postnatal">Postnatal</button>
                    </div>
                    
                    <div class="service-grid">
                        <div class="service-card prenatal">
                            <div class="card-icon"><i class="fas fa-ultrasound"></i></div>
                            <h3>Ultrasound Consultations</h3>
                            <p>Virtual analysis of ultrasound results by specialists</p>
                        </div>
                        
                        <div class="service-card prenatal">
                            <div class="card-icon"><i class="fas fa-heartbeat"></i></div>
                            <h3>Fetal Monitoring</h3>
                            <p>At-home Doppler device integration with real-time tracking</p>
                        </div>
                        <!-- Add more service cards -->
                    </div>
                </section>

                <!-- Specialist Section -->
                <section id="experts" class="content-section">
                    <h2 class="section-title">Our Specialist Team</h2>
                    <div class="specialist-carousel">
                        <!-- Specialist cards will be populated here -->
                    </div>
                </section>
            </div>
        </div>
    </section>

    <!-- Emergency Floating Button -->
    <div class="pregnancy-emergency">
        <button class="emergency-btn">
            <i class="fas fa-bell"></i> Pregnancy Emergency
        </button>
    </div>
</main>

<?php include 'include/footer.php'; ?>
<?php include 'include/telebaby.php';?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/pregnancy-care.js"></script>
</body>
</html>