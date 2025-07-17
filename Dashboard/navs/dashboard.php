
<?php

include '../include/dashboard_header.php';
?>

<!-- Health Metrics Cards -->
<section class="metric-cards-container" >
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fas fa-heartbeat" style="color: #FF6B6B; font-size: 24px;"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">Heart Rate</span>
                    <span class="metric-value">72 BPM</span>
                    <span class="metric-trend trend-down"><i class="fas fa-arrow-down"></i> 5% from last week</span>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fas fa-weight" style="color: #4FD1C5; font-size: 24px;"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">Total Weight</span>
                    <span class="metric-value">78.5 KG</span>
                    <span class="metric-trend trend-up"><i class="fas fa-arrow-up"></i> 1.2% from last month</span>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fas fa-tint" style="color: #F6AD55; font-size: 24px;"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">Blood Pressure</span>
                    <span class="metric-value">120/80 mmHg</span>
                    <span class="metric-trend"><i class="fas fa-check"></i> Normal</span>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fas fa-fire" style="color: #63B3ED; font-size: 24px;"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-label">Calorie Burn</span>
                    <span class="metric-value">1850 kcal</span>
                    <span class="metric-trend trend-down"><i class="fas fa-arrow-down"></i> 8% from target</span>
                </div>
            </div>
        </section>

        <div class="main-dashboard-grid">
            <div class="left-column">
                <!-- Regular Checkup Schedule -->
                <section class="card regular-checkup-schedule">
                    <div class="card-header">
                        <h2>Regular Checkup Schedule</h2>
                        <div class="dropdown-filter">
                            <span class="selected-month">Oct 2024</span> <i class="fas fa-caret-down"></i>
                        </div>
                    </div>
                    <div class="schedule-calendar-days">
                        <i class="fas fa-angle-left nav-arrow"></i>
                        <div class="schedule-day"><span>FRI</span><span>09</span></div>
                        <div class="schedule-day"><span>SAT</span><span>10</span></div>
                        <div class="schedule-day"><span>SUN</span><span>11</span></div>
                        <div class="schedule-day active"><span>MON</span><span>12</span></div>
                        <div class="schedule-day"><span>TUE</span><span>13</span></div>
                        <div class="schedule-day"><span>WED</span><span>14</span></div>
                        <div class="schedule-day"><span>THU</span><span>15</span></div>
                        <div class="schedule-day"><span>FRI</span><span>16</span></div>
                        <div class="schedule-day"><span>SAT</span><span>17</span></div>
                        <div class="schedule-day"><span>SUN</span><span>18</span></div>
                        <i class="fas fa-angle-right nav-arrow"></i>
                    </div>
                    <div class="doctor-appointment-list">
                        <div class="appointment-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzNSIgY3k9IjM1IiByPSIzNSIgZmlsbD0iI0YwRjdGRiIvPjxjaXJjbGUgY3g9IjM1IiBjeT0iMjUiIHI9IjgiIGZpbGw9IiMxQTNBNUYiLz48cGF0aCBkPSJNMTUgNTBjMC0xMSAyMC0xMSAyMCAxMEgxNXoiIGZpbGw9IiMxQTNBNUYiLz48L3N2Zz4=" alt="Dr Dianne Russell" class="doctor-avatar">
                            <div class="appointment-details">
                                <h4>Dr Dianne Russell</h4>
                                <p class="specialty">Cardiologist</p>
                                <p class="time"><i class="far fa-clock"></i> Appointment: 10:00 AM</p>
                                <p class="description">Primary Concern: Persistent chest discomfort and irregular heartbeats.</p>
                                <button class="btn btn-google-meet"><i class="fas fa-video"></i> Google Meet Link</button>
                            </div>
                        </div>
                        <div class="appointment-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzAiIGhlaWdodD0iNzAiIHZpZXdCb3g9IjAgMCA3MCA3MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzNSIgY3k9IjM1IiByPSIzNSIgZmlsbD0iI0YwRjdGRiIvPjxjaXJjbGUgY3g9IjM1IiBjeT0iMjUiIHI9IjgiIGZpbGw9IiMyQTg2RkYiLz48cGF0aCBkPSJNMTUgNTBjMC0xMSAyMC0xMSAyMCAxMEgxNXoiIGZpbGw9IiMyQTg2RkYiLz48L3N2Zz4=" alt="Dr Devon Lane" class="doctor-avatar">
                            <div class="appointment-details">
                                <h4>Dr Devon Lane</h4>
                                <p class="specialty">Optometry</p>
                                <p class="time"><i class="far fa-clock"></i> Appointment: 2:00 PM</p>
                                <p class="description">Primary Concern: Persistent blurred vision and eye discomfort.</p>
                                <button class="btn btn-google-meet"><i class="fas fa-video"></i> Google Meet Link</button>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </section>

                <!-- Explore Departments -->
                <section class="card explore-departments">
                    <div class="card-header">
                        <h2>Explore Departments</h2>
                        <a href="#" class="see-more">See More</a>
                    </div>
                    <div class="department-grid">
                        <div class="department-item">
                            <i class="fas fa-heart" style="color: #FF6B6B; font-size: 30px;"></i>
                            <span>Cardiologist</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-tooth" style="color: #63B3ED; font-size: 30px;"></i>
                            <span>Dentist</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-brain" style="color: #F6AD55; font-size: 30px;"></i>
                            <span>Neurologist</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-brain" style="color: #9F7AEA; font-size: 30px;"></i>
                            <span>Psychologist</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-bone" style="color: #38B2AC; font-size: 30px;"></i>
                            <span>Orthopedic</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-eye" style="color: #4C51BF; font-size: 30px;"></i>
                            <span>Ophthalmology</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-lungs" style="color: #ED64A6; font-size: 30px;"></i>
                            <span>Pulmonology</span>
                        </div>
                        <div class="department-item">
                            <i class="fas fa-stomach" style="color: #48BB78; font-size: 30px;"></i>
                            <span>Gastroenterology</span>
                        </div>
                    </div>
                </section>

                <!-- Latest Health Tips -->
                <section class="card latest-health-tips">
                    <div class="card-header">
                        <h2>Latest Health Tips</h2>
                        <div class="carousel-nav">
                            <i class="fas fa-angle-left"></i>
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                    <div class="health-tips-grid">
                        <div class="health-tip-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjE2MCIgdmlld0JveD0iMCAwIDMwMCAxNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIxNjAiIGZpbGw9IiNGMEY3RkYiLz48dGV4dCB4PSIxNTAiIHk9IjgwIiBmb250LWZhbWlseT0iUG9wcGlucyIgZm9udC1zaXplPSIyNCIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0iIzFBUzRBNiIgdGV4dC1hbmNob3I9Im1pZGRsZSI+SW1tdW5lIFN5c3RlbSBUaXBzPC90ZXh0Pjwvc3ZnPg==" alt="Immune System Tip">
                            <div class="tip-content">
                                <h3>Daily Health Tips To Strengthen Your Immune System</h3>
                                <p class="author">By Dimshad Hassan</p>
                            </div>
                        </div>
                        <div class="health-tip-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjE2MCIgdmlld0JveD0iMCAwIDMwMCAxNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIxNjAiIGZpbGw9IiNGMEY3RkYiLz48dGV4dCB4PSIxNTAiIHk9IjgwIiBmb250LWZhbWlseT0iUG9wcGlucyIgZm9udC1zaXplPSIyNCIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0iI0Y2QUQ1NSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+U3RyZXNzIE1hbmFnZW1lbnQ8L3RleHQ+PC9zdmc+" alt="Stress Management Tip">
                            <div class="tip-content">
                                <h3>Tips For Reducing Stress & Enhancing Mental Clarity</h3>
                                <p class="author">By Humayun Kabir</p>
                            </div>
                        </div>
                        <div class="health-tip-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjE2MCIgdmlld0JveD0iMCAwIDMwMCAxNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjMwMCIgaGVpZ2h0PSIxNjAiIGZpbGw9IiNGMEY3RkYiLz48dGV4dCB4PSIxNTAiIHk9IjgwIiBmb250LWZhbWlseT0iUG9wcGlucyIgZm9udC1zaXplPSIyNCIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0iI0Y2NkI2QiIgdGV4dC1hbmNob3I9Im1pZGRsZSI+SGVhcnQgSGVhbHRoIFRpcHM8L3RleHQ+PC9zdmc+" alt="Heart Health Tip">
                            <div class="tip-content">
                                <h3>Health Tips To Keep Your Heart In Peak Condition</h3>
                                <p class="author">By Navid Mahbub</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="right-column">
                <!-- Need Doctor Consultation -->
                <section class="card need-doctor-consultation">
                    <div class="card-header">
                        <h2>Need Doctor Consultation?</h2>
                        <div class="month-selector">
                            <i class="fas fa-angle-left"></i>
                            <span>Oct 2024</span> <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                    <div class="calendar-grid">
                        <span class="day-name">Su</span>
                        <span class="day-name">Mo</span>
                        <span class="day-name">Tu</span>
                        <span class="day-name">We</span>
                        <span class="day-name">Th</span>
                        <span class="day-name">Fr</span>
                        <span class="day-name">Sa</span>
                        <span class="day-num">29</span>
                        <span class="day-num">30</span>
                        <span class="day-num">1</span>
                        <span class="day-num">2</span>
                        <span class="day-num">3</span>
                        <span class="day-num">4</span>
                        <span class="day-num">5</span>
                        <span class="day-num">6</span>
                        <span class="day-num">7</span>
                        <span class="day-num">8</span>
                        <span class="day-num">9</span>
                        <span class="day-num">10</span>
                        <span class="day-num">11</span>
                        <span class="day-num">12</span>
                        <span class="day-num active-date has-appointment">13</span>
                        <span class="day-num">14</span>
                        <span class="day-num">15</span>
                        <span class="day-num">16</span>
                        <span class="day-num">17</span>
                        <span class="day-num">18</span>
                        <span class="day-num">19</span>
                        <span class="day-num">20</span>
                        <span class="day-num">21</span>
                        <span class="day-num">22</span>
                        <span class="day-num">23</span>
                        <span class="day-num">24</span>
                        <span class="day-num">25</span>
                        <span class="day-num">26</span>
                        <span class="day-num">27</span>
                        <span class="day-num">28</span>
                        <span class="day-num">29</span>
                        <span class="day-num">30</span>
                        <span class="day-num">31</span>
                    </div>
                    <button class="btn btn-add-appointment"><i class="fas fa-plus"></i> Add New Appointment</button>
                </section>

                <!-- Popular Doctors -->
                <section class="card popular-doctors">
                    <h2>Popular Doctors</h2>
                    <div class="doctor-list">
                        <div class="popular-doctor-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIzMCIgZmlsbD0iI0YwRjdGRiIvPjxjaXJjbGUgY3g9IjMwIiBjeT0iMjIiIHI9IjciIGZpbGw9IiMyQTg2RkYiLz48cGF0aCBkPSJNMTIgNDVjMC05IDE4LTkgMTggOEgxMnEiIGZpbGw9IiMyQTg2RkYiLz48L3N2Zz4=" alt="Dr Nguyen Lee" class="doctor-avatar">
                            <div class="doctor-info">
                                <h4>Dr Nguyen Lee</h4>
                                <p class="specialty">Cardiologist</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i> <span>4.8 (5.6K Review)</span>
                                </div>
                            </div>
                            <i class="fas fa-external-link-alt visit-profile"></i>
                        </div>
                        <div class="popular-doctor-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIzMCIgZmlsbD0iI0YwRjdGRiIvPjxjaXJjbGUgY3g9IjMwIiBjeT0iMjIiIHI9IjciIGZpbGw9IiNGNkFENTUiLz48cGF0aCBkPSJNMTIgNDVjMC05IDE4LTkgMTggOEgxMnEiIGZpbGw9IiNGNkFENTUiLz48L3N2Zz4=" alt="Dr Savannah Nguyen" class="doctor-avatar">
                            <div class="doctor-info">
                                <h4>Dr Savannah Nguyen</h4>
                                <p class="specialty">Optometry</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i> <span>5.0 (3.2K Review)</span>
                                </div>
                            </div>
                            <i class="fas fa-external-link-alt visit-profile"></i>
                        </div>
                        <div class="popular-doctor-item">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIzMCIgZmlsbD0iI0YwRjdGRiIvPjxjaXJjbGUgY3g9IjMwIiBjeT0iMjIiIHI9IjciIGZpbGw9IiM2M0IzRUQiLz48cGF0aCBkPSJNMTIgNDVjMC05IDE4LTkgMTggOEgxMnEiIGZpbGw9IiM2M0IzRUQiLz48L3N2Zz4=" alt="Dr Theresa Webb" class="doctor-avatar">
                            <div class="doctor-info">
                                <h4>Dr Theresa Webb</h4>
                                <p class="specialty">Dentist</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i> <span>4.9 (5.1K Review)</span>
                                </div>
                            </div>
                            <i class="fas fa-external-link-alt visit-profile"></i>
                        </div>
                    </div>
                    <button class="btn btn-explore-more">Explore More</button>
                </section>

                <!-- AI Doctor Chat -->
                <section class="card chat-ai-doctor">
                    <div class="ai-chat-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ai-chat-content">
                        <h3>Chat with AI Doctor?</h3>
                        <p>Get instant medical advice for common health concerns</p>
                    </div>
                    <button class="btn btn-chat-now">Chat Now</button>
                </section>

                <!-- Health Data Trends -->
                <section class="card">
                    <div class="card-header">
                        <h2>Health Data Trends</h2>
                        <div class="dropdown-filter">
                            <span>Last 7 Days</span> <i class="fas fa-caret-down"></i>
                        </div>
                    </div>
                    <div class="health-chart">
                        <canvas id="healthChart"></canvas>
                    </div>
                </section>
            </div>
        </div>
    </div>
    </div>
    
<script>

</script>

    
    
