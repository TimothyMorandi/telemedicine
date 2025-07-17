<?php
// Include the header file
include 'include/head.php';
include 'include/header.php';
?>

    <section
        class="hero-section hero-section-small"
        style="background-image: url('images/health_events.jpg');"
    >
        <div class="hero-overlay"></div>
        <div class="hero-content hero-content-small">
            <h1>Health Events Calendar</h1>
        </div>
    </section>

    <section class="health-events-calendar-section">
        <div class="container">
            <h2 class="section-title">Health Events Calendar</h2>

            <div class="tabs-container">
                <button class="tab-button active" data-tab="prime-events">Telemedicine Prime Events</button>
                <button class="tab-button" data-tab="baby-events">Telemedicine Baby Events</button>
                <button class="tab-button" data-tab="hospital-events">Hospital Events</button>
            </div>

            <div id="prime-events" class="event-details-tab-content active">
                <div class="event-details-container">
                    <div class="event-detail-item">
                        <span class="detail-label">Date :</span> <span class="detail-value">Ongoing</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Location :</span> <span class="detail-value">Website</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Event :</span> <span class="detail-value">Podcast</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Topic/Campaign :</span> <span class="detail-value">6 Episodes focusing on Families with Young Kids.</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Registration :</span> <span class="detail-value"><a href="https://www.mediclinic.co.za/en/corporate/mediclinicprime/the-health-wrap.html" target="_blank" class="registration-link">https://www.mediclinic.co.za/en/corporate/mediclinicprime/the-health-wrap.html</a></span>
                    </div>
                </div>
            </div>

            <div id="baby-events" class="event-details-tab-content hidden" style="background-color: #ffe4e1;">
                <div class="event-details-container">
                    <div class="event-detail-item">
                        <span class="detail-label">Date :</span> <span class="detail-value">Saturday, 25 January – Sunday, 26 January 2025</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Location :</span> <span class="detail-value">Vimeo Webinar</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Event :</span> <span class="detail-value">Prepare for Parenthood Antenatal Course</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Topic/Campaign :</span> <span class="detail-value">
                            Saturday<br>
                            09:00 – 12:30: Birthing Options, Pain Relief, Postnatal Depression<br>
                            12:30 - 13:30: Contraception after birth<br>
                            Sunday<br>
                            09:00 – 10:15 Baby Development<br>
                            10:15 – 12:30 Breastfeeding, Baby Care
                        </span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Registration :</span> <span class="detail-value"><a href="https://mediclinic.zoom.us/webinar/register/WN_orPY-FyTQOyxmAbtPsZqFw" target="_blank" class="registration-link">https://mediclinic.zoom.us/webinar/register/WN_orPY-FyTQOyxmAbtPsZqFw</a></span>
                    </div>
                </div>
            </div>

            <div id="hospital-events" class="event-details-tab-content hidden">
                <div class="event-details-container">
                    <div class="event-detail-item">
                        <span class="detail-label">Date :</span> <span class="detail-value">Monthly</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Location :</span> <span class="detail-value">Local Mediclinic Hospitals</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Event :</span> <span class="detail-value">Community Health Screenings</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Topic/Campaign :</span> <span class="detail-value">Blood Pressure, Cholesterol, Glucose Checks</span>
                    </div>
                    <div class="event-detail-item">
                        <span class="detail-label">Registration :</span> <span class="detail-value">Walk-ins welcome.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'include/footer.php'; ?>
<?php include 'include/Health_eventscript.php'; ?>