<?php include 'include/head.php'; ?>
<?php include 'include/header.php'; ?>
    <main>
        <section class="hero-sec hero-primary-care" style="background-image: linear-gradient(135deg, rgba(12, 115, 185, 0.9), rgba(67, 206, 162, 0.8)), url('images/primary.jpg')">
        <div class="container hero-content">
                <div class="hero-text-block">
                    <h1>Advanced Telemedicine Care</h1>
                    <p>Comprehensive Virtual Healthcare Solutions</p>
                    <div class="hero-cta-group">
                        <a href="createaccount.php" class="btn btn-hero"><i class="fas fa-video"></i> Start Video Consultation</a>
                        <a href="#services" class="btn btn-hero-outline">Explore Services</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="breadcrumbs">
        <div class="container">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Advanced Telecare</span>
            </div>
        </section>

        <!-- Floating Navigation -->
        <nav class="floating-nav">
            <div class="container">
                <a href="#services"><i class="fas fa-procedures"></i> Services</a>
                <a href="#hiv-care"><i class="fas fa-ribbon"></i> HIV Care</a>
                <a href="#clinics"><i class="fas fa-hospital"></i> Clinics</a>
                <a href="#monitoring"><i class="fas fa-heartbeat"></i> Monitoring</a>
            </div>
        </nav>

        <section class="main-content-area">
            <div class="container content-grid">
                <aside class="sidebar-nav">
                    <nav>
                        <ul>
                            <li class="active"><a href="#services"><i class="fas fa-stethoscope"></i> Our Services</a></li>
                            <li><a href="#hiv-care"><i class="fas fa-ribbon"></i> HIV/AIDS Care</a></li>
                            <li><a href="#clinics"><i class="fas fa-map-marker-alt"></i> Partner Clinics</a></li>
                            <li><a href="#monitoring"><i class="fas fa-chart-line"></i> Health Monitoring</a></li>
                            <li><a href="#pharmacy"><i class="fas fa-prescription-bottle-alt"></i> E-Pharmacy</a></li>
                            <li><a href="#emergency"><i class="fas fa-ambulance"></i> Emergency Care</a></li>
                        </ul>
                    </nav>
                </aside>

                <div class="main-content-body">
                    <!-- Services Section -->
                    <article id="services" class="content-section">
                        <h2 class="section-title">Advanced Telemedicine Services</h2>
                        <div class="service-categories">
                            <div class="category-card">
                                <h3><i class="fas fa-user-md"></i> Core Services</h3>
                                <ul>
                                    <li>24/7 Virtual Consultations</li>
                                    <li>Chronic Disease Management</li>
                                    <li>Mental Health Support</li>
                                    <li>Pediatric Care</li>
                                </ul>
                            </div>
                            

                    <!-- HIV/AIDS Care Section -->
                    <article id="hiv-care" class="content-section">
                        <h2 class="section-title">Comprehensive HIV/AIDS Care</h2>
                        <div class="hiv-care-grid">
                            <div class="hiv-service-card">
                                <div class="hiv-service-icon">
                                    <i class="fas fa-virus"></i>
                                </div>
                                <h3>Testing & Diagnosis</h3>
                                <ul class="hiv-service-list">
                                    <li>Rapid HIV Testing Kits</li>
                                    <li>Confidential Online Counseling</li>
                                    <li>PCR Testing Coordination</li>
                                    <li>Post-Test Support</li>
                                </ul>
                            </div>

                            <div class="hiv-service-card">
                                <div class="hiv-service-icon">
                                    <i class="fas fa-prescription-bottle-alt"></i>
                                </div>
                                <h3>Treatment Management</h3>
                                <ul class="hiv-service-list">
                                    <li>ART Adherence Monitoring</li>
                                    <li>Viral Load Tracking</li>
                                    <li>Side Effect Management</li>
                                    <li>Drug Resistance Testing</li>
                                </ul>
                            </div>

                            <div class="hiv-cta-box">
                                <div class="privacy-assurance">
                                    <i class="fas fa-user-shield"></i>
                                    <p>100% Confidential Services</p>
                                </div>
                                <a href="createaccount.php" class="btn btn-hiv-cta">
                                    <i class="fas fa-user-lock"></i> Register for Confidential Care
                                </a>
                            </div>
                        </div>
                    </article>

                    <article id="clinics" class="content-section">
                        <h2 class="section-title">Certified Partner Clinics</h2>
                        <div class="clinic-filters">
                            <button class="filter-btn active" data-filter="all">All</button>
                            <button class="filter-btn" data-filter="pediatrics">Pediatrics</button>
                            <button class="filter-btn" data-filter="emergency">Emergency</button>
                            <button class="filter-btn" data-filter="chronic">Chronic Care</button>
                        </div>
                        <div class="clinic-grid">
                            <!-- Clinic cards will be populated via JS -->
                        </div>
                    </article>

                    <!-- Health Monitoring -->
                    <article id="monitoring" class="content-section">
                        <h2 class="section-title">Health Monitoring Dashboard</h2>
                        <div class="health-metrics">
                            <div class="metric-card">
                                <h3><i class="fas fa-heart"></i> Vital Signs</h3>
                                <canvas id="vitalChart"></canvas>
                            </div>
                            <div class="metric-card">
                                <h3><i class="fas fa-pills"></i> Medication Adherence</h3>
                                <div class="adherence-meter"></div>
                            </div>
                        </div>
                    </article>

                    <!-- E-Pharmacy -->
                    <article id="pharmacy" class="content-section">
                        <h2 class="section-title">Digital Pharmacy Services</h2>
                        <div class="pharmacy-services">
                            <div class="pharmacy-card">
                                <i class="fas fa-prescription"></i>
                                <h3>E-Prescriptions</h3>
                                <p>Instant digital prescriptions sent to your preferred pharmacy</p>
                            </div>
                            <div class="pharmacy-card">
                                <i class="fas fa-clock"></i>
                                <h3>Medication Reminders</h3>
                                <p>Customizable reminders and refill alerts</p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

        </section>
    </main>

    <?php include 'include/footer.php'; ?>
    <?php include 'include/scripts.php'; ?>
    <?php include 'include/primary.php';?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>