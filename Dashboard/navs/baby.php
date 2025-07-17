
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baby Care & Vaccination Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fff9f9 0%, #f0f9ff 100%);
            color: #1a237e;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            text-align: center;
            padding: 30px 0;
            position: relative;
        }
        
        .dashboard-title {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: #5c6bc0;
            margin-bottom: 10px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .dashboard-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: #ff80ab;
            border-radius: 2px;
        }
        
        .dashboard-subtitle {
            color: #3949ab;
            max-width: 800px;
            margin: 0 auto 30px;
            font-size: 18px;
            line-height: 1.7;
        }
        
        .registration-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin: 0 auto 40px;
            max-width: 800px;
            text-align: center;
        }
        
        .registration-title {
            font-size: 28px;
            color: #5c6bc0;
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }
        
        .registration-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .form-group {
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #5c6bc0;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e7ff;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: #5c6bc0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(92, 107, 192, 0.2);
        }
        
        .register-btn {
            background: linear-gradient(135deg, #5c6bc0, #3949ab);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(92, 107, 192, 0.3);
        }
        
        .registration-note {
            margin-top: 20px;
            color: #7986cb;
            font-size: 14px;
            font-style: italic;
        }
        
        .baby-info-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin: 0 auto 40px;
            max-width: 800px;
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .baby-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5c6bc0, #ff80ab);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            font-weight: 600;
        }
        
        .baby-details {
            flex: 1;
            min-width: 300px;
        }
        
        .baby-name {
            font-size: 32px;
            color: #5c6bc0;
            margin-bottom: 10px;
        }
        
        .baby-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-card {
            background: #f5f7ff;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e0e7ff;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #5c6bc0;
        }
        
        .stat-label {
            color: #7986cb;
            font-size: 14px;
        }
        
        .tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            background: #e8eaf6;
            border: none;
            padding: 14px 28px;
            border-radius: 30px;
            font-size: 17px;
            font-weight: 600;
            color: #5c6bc0;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tab-btn.active {
            background: linear-gradient(135deg, #5c6bc0, #3949ab);
            color: white;
            box-shadow: 0 4px 12px rgba(92, 107, 192, 0.3);
        }
        
        .tab-btn:hover:not(.active) {
            background: #d1d9ff;
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .vaccine-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 28px;
            color: #5c6bc0;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-family: 'Playfair Display', serif;
        }
        
        .vaccine-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .vaccine-card {
            background: #f5f7ff;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e0e7ff;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .vaccine-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(92, 107, 192, 0.15);
        }
        
        .vaccine-name {
            font-size: 20px;
            color: #5c6bc0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .vaccine-details {
            color: #3949ab;
            margin-bottom: 15px;
            font-size: 15px;
        }
        
        .vaccine-status {
            background: #e8eaf6;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-due {
            background: #fff9c4;
            color: #f57f17;
        }
        
        .status-completed {
            background: #c8e6c9;
            color: #388e3c;
        }
        
        .status-upcoming {
            background: #bbdefb;
            color: #1976d2;
        }
        
        .vaccine-date {
            margin-top: 15px;
            font-weight: 600;
            color: #5c6bc0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff80ab;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .growth-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin-bottom: 40px;
        }
        
        .growth-chart {
            height: 300px;
            background: #f8fbff;
            border-radius: 12px;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #e0e7ff;
            position: relative;
            overflow: hidden;
        }
        
        .chart-grid {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            grid-template-rows: repeat(5, 1fr);
        }
        
        .grid-line {
            border-right: 1px dashed #e0e7ff;
            border-bottom: 1px dashed #e0e7ff;
        }
        
        .chart-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            color: #7986cb;
            font-size: 14px;
        }
        
        .chart-legend {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            justify-content: center;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        .feeding-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin-bottom: 40px;
        }
        
        .feeding-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        .feeding-card {
            background: #f5f7ff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            border: 1px solid #e0e7ff;
            transition: all 0.3s ease;
        }
        
        .feeding-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(92, 107, 192, 0.15);
        }
        
        .feeding-icon {
            font-size: 48px;
            color: #5c6bc0;
            margin-bottom: 15px;
        }
        
        .feeding-title {
            font-size: 20px;
            color: #5c6bc0;
            margin-bottom: 10px;
        }
        
        .feeding-value {
            font-size: 32px;
            font-weight: 700;
            color: #5c6bc0;
            margin: 10px 0;
        }
        
        .feeding-desc {
            color: #7986cb;
            font-size: 15px;
        }
        
        .milestones-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin-bottom: 40px;
        }
        
        .milestones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        .milestone-card {
            background: #f5f7ff;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e0e7ff;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .milestone-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(92, 107, 192, 0.15);
        }
        
        .milestone-title {
            font-size: 18px;
            color: #5c6bc0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .milestone-desc {
            color: #3949ab;
            font-size: 15px;
            margin-bottom: 15px;
        }
        
        .milestone-date {
            background: #e8eaf6;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkmark {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #c8e6c9;
            color: #388e3c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .resources-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(92, 107, 192, 0.1);
            margin-bottom: 40px;
        }
        
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        .resource-card {
            background: #f5f7ff;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e0e7ff;
            transition: all 0.3s ease;
        }
        
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(92, 107, 192, 0.15);
        }
        
        .resource-title {
            font-size: 18px;
            color: #5c6bc0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .resource-desc {
            color: #3949ab;
            font-size: 15px;
            margin-bottom: 15px;
        }
        
        .resource-link {
            color: #5c6bc0;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .resource-link:hover {
            color: #3949ab;
            text-decoration: underline;
        }
        
        footer {
            text-align: center;
            padding: 30px 0;
            color: #7986cb;
            font-size: 14px;
            border-top: 1px solid #e0e7ff;
            margin-top: 40px;
        }
        
        footer a { 
            color: #5c6bc0; 
            text-decoration: none;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 32px;
            }
            
            .baby-info-card {
                padding: 20px;
                text-align: center;
                justify-content: center;
            }
            
            .tab-btn {
                padding: 12px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1 class="dashboard-title">Baby Care & Vaccination Tracker</h1>
            <p class="dashboard-subtitle">Track your baby's growth, vaccination schedule, feeding patterns, and developmental milestones all in one place</p>
        </header>
        
        <!-- Baby Registration Section -->
        <div class="registration-section">
            <h2 class="registration-title">Register Your Baby</h2>
            <div class="registration-form">
                <div class="form-group">
                    <label for="baby-name"><i class="fas fa-baby"></i> Baby's Full Name</label>
                    <input type="text" id="baby-name" placeholder="Enter baby's full name" value="Emma Johnson">
                </div>
                <div class="form-group">
                    <label for="birth-date"><i class="fas fa-calendar-alt"></i> Date of Birth</label>
                    <input type="date" id="birth-date" value="2025-05-15">
                </div>
                <button class="register-btn" id="register-baby">
                    <i class="fas fa-user-plus"></i> Register Baby
                </button>
            </div>
            <p class="registration-note">
                <i class="fas fa-info-circle"></i> Once you register your baby, the name will be reflected throughout the dashboard
            </p>
        </div>
        
        <!-- Baby Information Card -->
        <div class="baby-info-card">
            <div class="baby-avatar">E</div>
            <div class="baby-details">
                <div class="baby-name" id="registered-name">Baby Emma Johnson</div>
                <div style="color: #7986cb; margin-bottom: 15px;">
                    <i class="fas fa-birthday-cake"></i> Born: May 15, 2025 (3 months old)
                </div>
                <div class="baby-stats">
                    <div class="stat-card">
                        <div class="stat-value">6.2 kg</div>
                        <div class="stat-label">Weight</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">61 cm</div>
                        <div class="stat-label">Height</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">39 cm</div>
                        <div class="stat-label">Head Circumference</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">75%</div>
                        <div class="stat-label">Percentile</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="vaccines"><i class="fas fa-syringe"></i> Vaccines</button>
            <button class="tab-btn" data-tab="growth"><i class="fas fa-chart-line"></i> Growth</button>
            <button class="tab-btn" data-tab="feeding"><i class="fas fa-baby-bottle"></i> Feeding</button>
            <button class="tab-btn" data-tab="milestones"><i class="fas fa-star"></i> Milestones</button>
            <button class="tab-btn" data-tab="resources"><i class="fas fa-book"></i> Resources</button>
        </div>
        
        <!-- Vaccines Tab -->
        <div class="tab-content active" id="vaccines">
            <div class="vaccine-section">
                <h2 class="section-title"><i class="fas fa-syringe"></i> Vaccination Schedule</h2>
                <div class="vaccine-grid">
                    <div class="vaccine-card">
                        <div class="badge">Next Due</div>
                        <div class="vaccine-name"><i class="fas fa-vial"></i> Rotavirus Vaccine</div>
                        <div class="vaccine-details">Protects against rotavirus infections that cause severe diarrhea</div>
                        <div class="vaccine-status status-due">Due in 2 weeks</div>
                        <div class="vaccine-date"><i class="fas fa-calendar"></i> Recommended at 4 months</div>
                    </div>
                    
                    <div class="vaccine-card">
                        <div class="vaccine-name"><i class="fas fa-vial"></i> DTaP Vaccine</div>
                        <div class="vaccine-details">Protects against diphtheria, tetanus, and pertussis (whooping cough)</div>
                        <div class="vaccine-status status-completed">Completed</div>
                        <div class="vaccine-date"><i class="fas fa-calendar"></i> Given at 2 months</div>
                    </div>
                    
                    <div class="vaccine-card">
                        <div class="vaccine-name"><i class="fas fa-vial"></i> Hib Vaccine</div>
                        <div class="vaccine-details">Protects against Haemophilus influenzae type b</div>
                        <div class="vaccine-status status-completed">Completed</div>
                        <div class="vaccine-date"><i class="fas fa-calendar"></i> Given at 2 months</div>
                    </div>
                    
                    <div class="vaccine-card">
                        <div class="vaccine-name"><i class="fas fa-vial"></i> Polio Vaccine</div>
                        <div class="vaccine-details">Protects against poliovirus</div>
                        <div class="vaccine-status status-upcoming">Upcoming</div>
                        <div class="vaccine-date"><i class="fas fa-calendar"></i> Next dose at 4 months</div>
                    </div>
                    
                    <div class="vaccine-card">
                        <div class="vaccine-name"><i class="fas fa-vial"></i> PCV13 Vaccine</div>
                        <div class="vaccine-details">Protects against pneumococcal disease</div>
                        <div class="vaccine-status status-completed">Completed</div>
                        <div class="vaccine-date"><i class="fas fa-calendar"></i> Given at 2 months</div>
                    </div>
                    
                    <div class="vaccine-card">
                        <div class="vaccine-name"><i class="fas fa-vial"></i> Hepatitis B Vaccine</div>
                        <div class="vaccine-details">Protects against hepatitis B virus</div>
                        <div class="vaccine-status status-completed">Completed</div>
                        <div class="vaccine-date"><i class="fas fa-calendar"></i> Given at birth</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Growth Tab -->
        <div class="tab-content" id="growth">
            <div class="growth-section">
                <h2 class="section-title"><i class="fas fa-chart-line"></i> Growth Tracking</h2>
                <div class="growth-chart">
                    <div class="chart-grid">
                        <!-- Grid lines will be generated here -->
                    </div>
                </div>
                <div class="chart-labels">
                    <div>Birth</div>
                    <div>1m</div>
                    <div>2m</div>
                    <div>3m</div>
                    <div>4m</div>
                    <div>5m</div>
                    <div>6m</div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #5c6bc0;"></div>
                        <div>Weight (kg)</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ff80ab;"></div>
                        <div>Height (cm)</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #81c784;"></div>
                        <div>Head Circumference (cm)</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feeding Tab -->
        <div class="tab-content" id="feeding">
            <div class="feeding-section">
                <h2 class="section-title"><i class="fas fa-baby-bottle"></i> Feeding Tracker</h2>
                <div class="feeding-grid">
                    <div class="feeding-card">
                        <div class="feeding-icon"><i class="fas fa-bottle-droplet"></i></div>
                        <div class="feeding-title">Today's Feeding</div>
                        <div class="feeding-value">6</div>
                        <div class="feeding-desc">times so far today</div>
                    </div>
                    
                    <div class="feeding-card">
                        <div class="feeding-icon"><i class="fas fa-clock"></i></div>
                        <div class="feeding-title">Last Feeding</div>
                        <div class="feeding-value">2:30 PM</div>
                        <div class="feeding-desc">about 1 hour ago</div>
                    </div>
                    
                    <div class="feeding-card">
                        <div class="feeding-icon"><i class="fas fa-wine-bottle"></i></div>
                        <div class="feeding-title">Average Intake</div>
                        <div class="feeding-value">120 ml</div>
                        <div class="feeding-desc">per feeding session</div>
                    </div>
                    
                    <div class="feeding-card">
                        <div class="feeding-icon"><i class="fas fa-moon"></i></div>
                        <div class="feeding-title">Night Feedings</div>
                        <div class="feeding-value">2</div>
                        <div class="feeding-desc">times per night</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Milestones Tab -->
        <div class="tab-content" id="milestones">
            <div class="milestones-section">
                <h2 class="section-title"><i class="fas fa-star"></i> Developmental Milestones</h2>
                <div class="milestones-grid">
                    <div class="milestone-card">
                        <div class="checkmark"><i class="fas fa-check"></i></div>
                        <div class="milestone-title"><i class="fas fa-smile"></i> Social Smiles</div>
                        <div class="milestone-desc">Baby smiles in response to caregivers</div>
                        <div class="milestone-date"><i class="fas fa-calendar"></i> Achieved at 2 months</div>
                    </div>
                    
                    <div class="milestone-card">
                        <div class="checkmark"><i class="fas fa-check"></i></div>
                        <div class="milestone-title"><i class="fas fa-head-side-virus"></i> Head Control</div>
                        <div class="milestone-desc">Can hold head up briefly during tummy time</div>
                        <div class="milestone-date"><i class="fas fa-calendar"></i> Achieved at 2.5 months</div>
                    </div>
                    
                    <div class="milestone-card">
                        <div class="milestone-title"><i class="fas fa-hand-point-up"></i> Reaching for Objects</div>
                        <div class="milestone-desc">Attempts to reach for nearby toys</div>
                        <div class="milestone-date"><i class="fas fa-calendar"></i> Expected at 4 months</div>
                    </div>
                    
                    <div class="milestone-card">
                        <div class="milestone-title"><i class="fas fa-comments"></i> Babbling Sounds</div>
                        <div class="milestone-desc">Makes consonant sounds like "baba", "dada"</div>
                        <div class="milestone-date"><i class="fas fa-calendar"></i> Expected at 4-6 months</div>
                    </div>
                    
                    <div class="milestone-card">
                        <div class="milestone-title"><i class="fas fa-utensils"></i> Showing Interest in Food</div>
                        <div class="milestone-desc">Watches others eat with interest</div>
                        <div class="milestone-date"><i class="fas fa-calendar"></i> Expected at 5-6 months</div>
                    </div>
                    
                    <div class="milestone-card">
                        <div class="milestone-title"><i class="fas fa-couch"></i> Sitting Without Support</div>
                        <div class="milestone-desc">Can sit upright without assistance</div>
                        <div class="milestone-date"><i class="fas fa-calendar"></i> Expected at 6-8 months</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resources Tab -->
        <div class="tab-content" id="resources">
            <div class="resources-section">
                <h2 class="section-title"><i class="fas fa-book"></i> Baby Care Resources</h2>
                <div class="resources-grid">
                    <div class="resource-card">
                        <div class="resource-title"><i class="fas fa-file-pdf"></i> Vaccination Guide</div>
                        <div class="resource-desc">Complete schedule of recommended vaccines with detailed information</div>
                        <a href="#" class="resource-link">Download Guide <i class="fas fa-download"></i></a>
                    </div>
                    
                    <div class="resource-card">
                        <div class="resource-title"><i class="fas fa-video"></i> Baby Feeding Tutorials</div>
                        <div class="resource-desc">Video guides on breastfeeding, bottle feeding, and introducing solids</div>
                        <a href="#" class="resource-link">Watch Videos <i class="fas fa-play-circle"></i></a>
                    </div>
                    
                    <div class="resource-card">
                        <div class="resource-title"><i class="fas fa-book-medical"></i> Sleep Training Handbook</div>
                        <div class="resource-desc">Gentle methods to establish healthy sleep habits</div>
                        <a href="#" class="resource-link">Read Handbook <i class="fas fa-book-open"></i></a>
                    </div>
                    
                    <div class="resource-card">
                        <div class="resource-title"><i class="fas fa-child"></i> Developmental Activities</div>
                        <div class="resource-desc">Age-appropriate play ideas to support development</div>
                        <a href="#" class="resource-link">View Activities <i class="fas fa-gamepad"></i></a>
                    </div>
                    
                    <div class="resource-card">
                        <div class="resource-title"><i class="fas fa-first-aid"></i> Emergency Care Guide</div>
                        <div class="resource-desc">What to do in common baby emergencies</div>
                        <a href="#" class="resource-link">Access Guide <i class="fas fa-ambulance"></i></a>
                    </div>
                    
                    <div class="resource-card">
                        <div class="resource-title"><i class="fas fa-user-md"></i> Find a Pediatrician</div>
                        <div class="resource-desc">Directory of certified pediatricians in your area</div>
                        <a href="#" class="resource-link">Search Now <i class="fas fa-search"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <footer>
            <p>© 2025 Baby Care Tracker. All information provided is for educational purposes only. | 
            <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
            <p>For medical advice, always consult with your pediatrician.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Registration functionality
            const registerBtn = document.getElementById('register-baby');
            const babyNameInput = document.getElementById('baby-name');
            const registeredName = document.getElementById('registered-name');
            
            registerBtn.addEventListener('click', function() {
                const name = babyNameInput.value.trim();
                if (name) {
                    registeredName.textContent = name;
                    
                    // Update baby avatar with first initial
                    const avatar = document.querySelector('.baby-avatar');
                    avatar.textContent = name.charAt(0);
                    
                    // Show confirmation message
                    alert(`Baby ${name} successfully registered! The name is now reflected throughout the dashboard.`);
                } else {
                    alert('Please enter a name for your baby');
                }
            });
            
            // Tab functionality
            const tabBtns = document.querySelectorAll('.tab-btn');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    tabBtns.forEach(b => b.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Hide all tab contents
                    document.querySelectorAll('.tab-content').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    
                    // Show selected tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Generate grid lines for growth chart
            const gridContainer = document.querySelector('.chart-grid');
            for (let i = 0; i < 35; i++) {
                const gridLine = document.createElement('div');
                gridLine.classList.add('grid-line');
                gridContainer.appendChild(gridLine);
            }
            
            // Create growth chart data points
            function createDataPoint(top, left, color) {
                const point = document.createElement('div');
                point.style.position = 'absolute';
                point.style.top = top + '%';
                point.style.left = left + '%';
                point.style.width = '12px';
                point.style.height = '12px';
                point.style.backgroundColor = color;
                point.style.borderRadius = '50%';
                point.style.border = '2px solid white';
                point.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                document.querySelector('.growth-chart').appendChild(point);
                
                // Create connecting line
                if (left > 0) {
                    const line = document.createElement('div');
                    line.style.position = 'absolute';
                    line.style.top = 'calc(' + top + '% + 5px)';
                    line.style.left = (left - 5) + '%';
                    line.style.width = '5%';
                    line.style.height = '2px';
                    line.style.backgroundColor = color;
                    document.querySelector('.growth-chart').appendChild(line);
                }
            }
            
            // Weight data points
            createDataPoint(80, 5, '#5c6bc0');
            createDataPoint(75, 15, '#5c6bc0');
            createDataPoint(70, 25, '#5c6bc0');
            createDataPoint(65, 35, '#5c6bc0');
            createDataPoint(60, 45, '#5c6bc0');
            createDataPoint(55, 55, '#5c6bc0');
            createDataPoint(50, 65, '#5c6bc0');
            
            // Height data points
            createDataPoint(90, 5, '#ff80ab');
            createDataPoint(85, 15, '#ff80ab');
            createDataPoint(80, 25, '#ff80ab');
            createDataPoint(75, 35, '#ff80ab');
            createDataPoint(70, 45, '#ff80ab');
            createDataPoint(65, 55, '#ff80ab');
            createDataPoint(60, 65, '#ff80ab');
            
            // Head circumference data points
            createDataPoint(85, 5, '#81c784');
            createDataPoint(80, 15, '#81c784');
            createDataPoint(75, 25, '#81c784');
            createDataPoint(70, 35, '#81c784');
            createDataPoint(65, 45, '#81c784');
            createDataPoint(60, 55, '#81c784');
            createDataPoint(55, 65, '#81c784');
        });
    </script>
</body>
</html>