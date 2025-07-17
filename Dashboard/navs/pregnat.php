<?php
// Initialize variables
$gestational_age = "";
$due_date = "";
$progress_percentage = "";
$trimester = "";
$appointment_dates = array();
$educational_resources = array();

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conception_date'])) {
    // Get conception date from form
    $conception_date = new DateTime($_POST['conception_date']);
    $today = new DateTime();
    
    // Calculate weeks pregnant (gestational age)
    $diff = $today->diff($conception_date);
    $diff_weeks = floor($diff->days / 7);
    $gestational_age = $diff_weeks . ' Weeks';
    
    // Calculate due date (40 weeks from conception)
    $due_date_obj = clone $conception_date;
    $due_date_obj->modify('+280 days'); // 40 weeks * 7 days
    $due_date = $due_date_obj->format('M d, Y');
    
    // Calculate progress percentage
    $total_days = 280;
    $progress_percentage = min(100, floor(($diff->days / $total_days) * 100)) . '%';
    
    // Determine trimester
    if ($diff_weeks < 14) {
        $trimester = 'first';
    } elseif ($diff_weeks < 28) {
        $trimester = 'second';
    } elseif ($diff_weeks < 40) {
        $trimester = 'third';
    } else {
        $trimester = 'postpartum';
    }
    
    // Calculate appointment dates
    $appt1 = clone $conception_date;
    $appt1->modify('+30 days');
    $appointment_dates[0] = $appt1->format('M d, Y');
    
    $appt2 = clone $appt1;
    $appt2->modify('+14 days');
    $appointment_dates[1] = $appt2->format('M d, Y');
    
    $appt3 = clone $appt2;
    $appt3->modify('+14 days');
    $appointment_dates[2] = $appt3->format('M d, Y');
    
    $appt4 = clone $appt3;
    $appt4->modify('+14 days');
    $appointment_dates[3] = $appt4->format('M d, Y');
    
    // Set educational resources based on trimester
    switch($trimester) {
        case 'first':
            $educational_resources = array(
                array('type' => 'pdf', 'title' => 'Nutrition Guide for First Trimester', 'badge' => 'New'),
                array('type' => 'video', 'title' => 'Coping with Morning Sickness'),
                array('type' => 'book', 'title' => 'Understanding Early Pregnancy Changes'),
                array('type' => 'chart', 'title' => 'Fetal Development: Weeks 1-12'),
                array('type' => 'utensils', 'title' => 'Foods to Avoid During Pregnancy'),
                array('type' => 'pills', 'title' => 'Essential Prenatal Vitamins Guide')
            );
            break;
        case 'second':
            $educational_resources = array(
                array('type' => 'pdf', 'title' => 'Nutrition Guide for Second Trimester'),
                array('type' => 'video', 'title' => 'Safe Exercise Routines', 'badge' => 'Popular'),
                array('type' => 'book', 'title' => 'Preparing for Anatomy Scan'),
                array('type' => 'chart', 'title' => 'Fetal Development: Weeks 13-26'),
                array('type' => 'baby', 'title' => 'Feeling Baby Movements Guide'),
                array('type' => 'bed', 'title' => 'Sleeping Positions for Comfort')
            );
            break;
        case 'third':
            $educational_resources = array(
                array('type' => 'pdf', 'title' => 'Third Trimester Nutrition Guide'),
                array('type' => 'video', 'title' => 'Labor Preparation Exercises'),
                array('type' => 'book', 'title' => 'Creating Your Birth Plan', 'badge' => 'Essential'),
                array('type' => 'chart', 'title' => 'Fetal Development: Weeks 27-40'),
                array('type' => 'hospital', 'title' => 'Hospital Bag Checklist'),
                array('type' => 'heartbeat', 'title' => 'Signs of Labor Guide')
            );
            break;
        case 'postpartum':
            $educational_resources = array(
                array('type' => 'pdf', 'title' => 'Postpartum Recovery Guide'),
                array('type' => 'video', 'title' => 'Breastfeeding Techniques', 'badge' => 'New'),
                array('type' => 'book', 'title' => 'Newborn Care Essentials'),
                array('type' => 'chart', 'title' => 'Postpartum Mental Health'),
                array('type' => 'heart', 'title' => 'Bonding with Your Baby'),
                array('type' => 'utensils', 'title' => 'Nutrition for Breastfeeding')
            );
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pregnancy Journey Tracker</title>
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
            background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ff 100%);
            color: #0c2d48;
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
            color: #1a5f96;
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
            background: #5fa8d3;
            border-radius: 2px;
        }
        
        .dashboard-subtitle {
            color: #3a6a8c;
            max-width: 800px;
            margin: 0 auto 30px;
            font-size: 18px;
            line-height: 1.7;
        }
        
        .input-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 6px 15px rgba(26, 95, 150, 0.1);
            margin: 0 auto 40px;
            max-width: 600px;
            text-align: center;
            transition: all 0.5s ease;
        }
        
        .input-title {
            font-size: 28px;
            color: #1a5f96;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .input-group {
            margin-bottom: 30px;
            text-align: left;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 12px;
            color: #0c2d48;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .input-group input {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #c9e4f5;
            background: #f8fcff;
            font-size: 18px;
            color: #1a5f96;
            text-align: center;
        }
        
        .input-group input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
        }
        
        .btn {
            background: linear-gradient(135deg, #3498db, #1a5f96);
            color: white;
            padding: 16px 35px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.5);
        }
        
        .dashboard-content {
            display: <?php echo empty($gestational_age) ? 'none' : 'block'; ?>;
            animation: fadeIn 1s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pregnancy-info {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(26, 95, 150, 0.1);
            margin-bottom: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background: #f8fcff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            border: 1px solid #d1e8f9;
            transition: transform 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .info-card h3 {
            color: #1a5f96;
            font-size: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .info-card .value {
            font-size: 36px;
            font-weight: 700;
            color: #1a5f96;
            margin: 10px 0;
        }
        
        .info-card .label {
            color: #3a6a8c;
            font-size: 16px;
        }
        
        .progress-bar {
            height: 16px;
            background: #e1f0fa;
            border-radius: 8px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #5fa8d3, #1a5f96);
            border-radius: 8px;
            width: <?php echo isset($progress_percentage) ? str_replace('%', '', $progress_percentage) : '0'; ?>%;
        }
        
        .tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            background: #e6f4ff;
            border: none;
            padding: 14px 28px;
            border-radius: 30px;
            font-size: 17px;
            font-weight: 600;
            color: #1a5f96;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tab-btn.active {
            background: linear-gradient(135deg, #3498db, #1a5f96);
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .tab-btn:hover:not(.active) {
            background: #d1ebff;
        }
        
        .resources-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }
        
        .resource-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(26, 95, 150, 0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid #5fa8d3;
        }
        
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(26, 95, 150, 0.15);
        }
        
        .resource-card h3 {
            color: #1a5f96;
            font-size: 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e1f0fa;
        }
        
        .resource-list {
            list-style: none;
        }
        
        .resource-list li {
            padding: 12px 0;
            border-bottom: 1px solid #e1f0fa;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .resource-list li:last-child {
            border-bottom: none;
        }
        
        .resource-list a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .resource-list a:hover {
            color: #1a5f96;
            text-decoration: underline;
        }
        
        .appointment-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 12px 0;
        }
        
        .appointment-date {
            font-weight: 600;
            color: #1a5f96;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .appointment-type {
            color: #3a6a8c;
            margin-left: 28px;
        }
        
        .clinic-form {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0c2d48;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #c9e4f5;
            background: #f8fcff;
            font-size: 16px;
            color: #1a5f96;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .consultation-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(26, 95, 150, 0.1);
            margin: 40px 0;
        }
        
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        .doctor-card {
            background: #f8fcff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            border: 1px solid #d1e8f9;
            transition: all 0.3s ease;
        }
        
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(26, 95, 150, 0.15);
        }
        
        .doctor-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5fa8d3, #1a5f96);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: 600;
        }
        
        .doctor-name {
            font-size: 20px;
            color: #1a5f96;
            margin-bottom: 5px;
        }
        
        .doctor-specialty {
            color: #3a6a8c;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .doctor-rating {
            color: #f39c12;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .availability {
            background: #e6f4ff;
            color: #1a5f96;
            padding: 8px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .consult-btn {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .consult-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
        }
        
        .badge {
            background: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-left: 8px;
        }
        
        footer {
            text-align: center;
            padding: 30px 0;
            color: #3a6a8c;
            font-size: 14px;
            border-top: 1px solid #d1e8f9;
            margin-top: 40px;
        }
        
        footer a { 
            color: #1a5f96; 
            text-decoration: none;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 32px;
            }
            
            .input-section {
                padding: 25px;
            }
            
            .input-title {
                font-size: 24px;
            }
            
            .resources-section {
                grid-template-columns: 1fr;
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
            <h1 class="dashboard-title">Pregnancy Journey Tracker</h1>
            <p class="dashboard-subtitle">Enter your conception date to unlock personalized pregnancy tracking, stage-specific guidance, and valuable resources tailored to your journey</p>
        </header>
        
        <!-- Input Section -->
        <div class="input-section" id="input-section">
            <h2 class="input-title"><i class="fas fa-calendar-alt"></i> When Did Your Journey Begin?</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="conception-date"><i class="fas fa-baby"></i> Conception Date</label>
                    <input type="date" id="conception-date" name="conception_date" value="<?php echo isset($_POST['conception_date']) ? htmlspecialchars($_POST['conception_date']) : '2025-01-15'; ?>">
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-calculator"></i> Calculate My Pregnancy Journey
                </button>
            </form>
        </div>
        
        <!-- Dashboard Content (Shown after form submission) -->
        <div class="dashboard-content" id="dashboard-content">
            <!-- Pregnancy Overview -->
            <div class="pregnancy-info">
                <div class="info-card">
                    <h3><i class="fas fa-baby"></i> Gestational Age</h3>
                    <div class="value" id="gestational-age"><?php echo $gestational_age; ?></div>
                    <div class="label">How far along you are</div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-calendar-check"></i> Due Date</h3>
                    <div class="value" id="due-date"><?php echo $due_date; ?></div>
                    <div class="label">Estimated delivery date</div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-chart-line"></i> Progress</h3>
                    <div class="value" id="progress-percentage"><?php echo $progress_percentage; ?></div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div class="label">Completed of pregnancy journey</div>
                </div>
            </div>
            
            <!-- Stage Tabs -->
            <div class="tabs">
                <button class="tab-btn <?php echo ($trimester === 'first') ? 'active' : ''; ?>" data-tab="first"><i class="fas fa-seedling"></i> First Trimester</button>
                <button class="tab-btn <?php echo ($trimester === 'second') ? 'active' : ''; ?>" data-tab="second"><i class="fas fa-heartbeat"></i> Second Trimester</button>
                <button class="tab-btn <?php echo ($trimester === 'third') ? 'active' : ''; ?>" data-tab="third"><i class="fas fa-hourglass-end"></i> Third Trimester</button>
                <button class="tab-btn <?php echo ($trimester === 'postpartum') ? 'active' : ''; ?>" data-tab="postpartum"><i class="fas fa-baby"></i> Postpartum</button>
            </div>
            
            <!-- Resources Section -->
            <div class="resources-section">
                <div class="resource-card">
                    <h3><i class="fas fa-book-medical"></i> Educational Resources</h3>
                    <ul class="resource-list" id="educational-resources">
                        <?php foreach ($educational_resources as $resource): ?>
                        <li>
                            <i class="fas fa-<?php echo $resource['type']; ?>"></i> 
                            <a href="#">
                                <?php echo $resource['title']; ?>
                                <?php if (isset($resource['badge'])): ?>
                                <span class="badge"><?php echo $resource['badge']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="resource-card">
                    <h3><i class="fas fa-calendar-check"></i> Upcoming Appointments</h3>
                    <ul class="resource-list">
                        <li>
                            <div class="appointment-item">
                                <span class="appointment-date"><i class="fas fa-calendar-day"></i> <span id="appt-date-1"><?php echo isset($appointment_dates[0]) ? $appointment_dates[0] : 'June 28, 2025'; ?></span></span>
                                <span class="appointment-type">Monthly Prenatal Checkup</span>
                            </div>
                        </li>
                        <li>
                            <div class="appointment-item">
                                <span class="appointment-date"><i class="fas fa-calendar-day"></i> <span id="appt-date-2"><?php echo isset($appointment_dates[1]) ? $appointment_dates[1] : 'July 5, 2025'; ?></span></span>
                                <span class="appointment-type">Glucose Screening Test</span>
                            </div>
                        </li>
                        <li>
                            <div class="appointment-item">
                                <span class="appointment-date"><i class="fas fa-calendar-day"></i> <span id="appt-date-3"><?php echo isset($appointment_dates[2]) ? $appointment_dates[2] : 'July 12, 2025'; ?></span></span>
                                <span class="appointment-type">Ultrasound Scan</span>
                            </div>
                        </li>
                        <li>
                            <div class="appointment-item">
                                <span class="appointment-date"><i class="fas fa-calendar-day"></i> <span id="appt-date-4"><?php echo isset($appointment_dates[3]) ? $appointment_dates[3] : 'July 20, 2025'; ?></span></span>
                                <span class="appointment-type">Telemedicine Consultation</span>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="resource-card">
                    <h3><i class="fas fa-clinic-medical"></i> Clinic Registration</h3>
                    <div class="clinic-form">
                        <div class="form-group">
                            <label for="clinic-location"><i class="fas fa-map-marker-alt"></i> Preferred Clinic Location</label>
                            <select id="clinic-location">
                                <option value="">Select a clinic</option>
                                <option value="main">Main Maternity Center</option>
                                <option value="north">Northside Women's Clinic</option>
                                <option value="west">Westend Pregnancy Center</option>
                                <option value="downtown">Downtown Family Clinic</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment-type"><i class="fas fa-stethoscope"></i> Appointment Type</label>
                            <select id="appointment-type">
                                <option value="">Select appointment type</option>
                                <option value="prenatal">Prenatal Checkup</option>
                                <option value="ultrasound">Ultrasound Scan</option>
                                <option value="consultation">Doctor Consultation</option>
                                <option value="testing">Lab Testing</option>
                                <option value="class">Pregnancy Class</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred-date"><i class="fas fa-calendar-alt"></i> Preferred Date</label>
                            <input type="date" id="preferred-date">
                        </div>
                        
                        <button class="btn" id="register-btn">
                            <i class="fas fa-clipboard-check"></i> Register for Clinic Visit
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Online Consultation Section -->
            <div class="consultation-card">
                <h2 class="dashboard-title" style="font-size: 32px; text-align: center; margin-bottom: 30px;">
                    <i class="fas fa-video"></i> Online Consultation Services
                </h2>
                
                <div class="doctor-grid">
                    <div class="doctor-card">
                        <div class="doctor-avatar">RD</div>
                        <div class="doctor-name">Dr. Rebecca Davis</div>
                        <div class="doctor-specialty">OB-GYN Specialist</div>
                        <div class="doctor-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            4.7 (128 reviews)
                        </div>
                        <div class="availability">
                            <i class="fas fa-clock"></i> Available today
                        </div>
                        <button class="consult-btn">
                            <i class="fas fa-video"></i> Book Video Consultation
                        </button>
                    </div>
                    
                    <div class="doctor-card">
                        <div class="doctor-avatar">MP</div>
                        <div class="doctor-name">Dr. Michael Patel</div>
                        <div class="doctor-specialty">Maternal-Fetal Medicine</div>
                        <div class="doctor-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            4.9 (94 reviews)
                        </div>
                        <div class="availability">
                            <i class="fas fa-clock"></i> Available tomorrow
                        </div>
                        <button class="consult-btn">
                            <i class="fas fa-video"></i> Book Video Consultation
                        </button>
                    </div>
                    
                    <div class="doctor-card">
                        <div class="doctor-avatar">SJ</div>
                        <div class="doctor-name">Dr. Sarah Johnson</div>
                        <div class="doctor-specialty">Prenatal Nutritionist</div>
                        <div class="doctor-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            5.0 (86 reviews)
                        </div>
                        <div class="availability">
                            <i class="fas fa-clock"></i> Available now
                        </div>
                        <button class="consult-btn">
                            <i class="fas fa-video"></i> Book Video Consultation
                        </button>
                    </div>
                </div>
            </div>
            
            <footer>
                <p>© 2025 Pregnancy Journey Tracker. All information provided is for educational purposes only. | 
                <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a> | <a href="#">HIPAA Compliance</a></p>
                <p>24/7 Support: 1-800-MOM-CARE (1-800-666-2273) | Email: support@maternaljourney.org</p>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabBtns = document.querySelectorAll('.tab-btn');
            const educationalResources = document.getElementById('educational-resources');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    tabBtns.forEach(b => b.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Update resources based on selected trimester
                    const trimester = this.getAttribute('data-tab');
                    updateResources(trimester);
                });
            });
            
            // Update resources based on trimester
            function updateResources(trimester) {
                let resourcesHTML = '';
                
                switch(trimester) {
                    case 'first':
                        resourcesHTML = `
                            <li><i class="fas fa-file-pdf"></i> <a href="#">Nutrition Guide for First Trimester <span class="badge">New</span></a></li>
                            <li><i class="fas fa-file-video"></i> <a href="#">Coping with Morning Sickness</a></li>
                            <li><i class="fas fa-book"></i> <a href="#">Understanding Early Pregnancy Changes</a></li>
                            <li><i class="fas fa-chart-line"></i> <a href="#">Fetal Development: Weeks 1-12</a></li>
                            <li><i class="fas fa-utensils"></i> <a href="#">Foods to Avoid During Pregnancy</a></li>
                            <li><i class="fas fa-pills"></i> <a href="#">Essential Prenatal Vitamins Guide</a></li>
                        `;
                        break;
                    case 'second':
                        resourcesHTML = `
                            <li><i class="fas fa-file-pdf"></i> <a href="#">Nutrition Guide for Second Trimester</a></li>
                            <li><i class="fas fa-file-video"></i> <a href="#">Safe Exercise Routines <span class="badge">Popular</span></a></li>
                            <li><i class="fas fa-book"></i> <a href="#">Preparing for Anatomy Scan</a></li>
                            <li><i class="fas fa-chart-line"></i> <a href="#">Fetal Development: Weeks 13-26</a></li>
                            <li><i class="fas fa-baby"></i> <a href="#">Feeling Baby Movements Guide</a></li>
                            <li><i class="fas fa-bed"></i> <a href="#">Sleeping Positions for Comfort</a></li>
                        `;
                        break;
                    case 'third':
                        resourcesHTML = `
                            <li><i class="fas fa-file-pdf"></i> <a href="#">Third Trimester Nutrition Guide</a></li>
                            <li><i class="fas fa-file-video"></i> <a href="#">Labor Preparation Exercises</a></li>
                            <li><i class="fas fa-book"></i> <a href="#">Creating Your Birth Plan <span class="badge">Essential</span></a></li>
                            <li><i class="fas fa-chart-line"></i> <a href="#">Fetal Development: Weeks 27-40</a></li>
                            <li><i class="fas fa-hospital"></i> <a href="#">Hospital Bag Checklist</a></li>
                            <li><i class="fas fa-heartbeat"></i> <a href="#">Signs of Labor Guide</a></li>
                        `;
                        break;
                    case 'postpartum':
                        resourcesHTML = `
                            <li><i class="fas fa-file-pdf"></i> <a href="#">Postpartum Recovery Guide</a></li>
                            <li><i class="fas fa-file-video"></i> <a href="#">Breastfeeding Techniques <span class="badge">New</span></a></li>
                            <li><i class="fas fa-book"></i> <a href="#">Newborn Care Essentials</a></li>
                            <li><i class="fas fa-chart-line"></i> <a href="#">Postpartum Mental Health</a></li>
                            <li><i class="fas fa-heart"></i> <a href="#">Bonding with Your Baby</a></li>
                            <li><i class="fas fa-utensils"></i> <a href="#">Nutrition for Breastfeeding</a></li>
                        `;
                        break;
                }
                
                educationalResources.innerHTML = resourcesHTML;
            }
            
            // Clinic registration functionality
            const registerBtn = document.getElementById('register-btn');
            
            registerBtn.addEventListener('click', function() {
                const clinicLocation = document.getElementById('clinic-location').value;
                const appointmentType = document.getElementById('appointment-type').value;
                const preferredDate = document.getElementById('preferred-date').value;
                
                if (!clinicLocation || !appointmentType || !preferredDate) {
                    alert('Please fill in all fields to register for a clinic visit');
                    return;
                }
                
                // In a real application, this would submit to a server
                alert(`Thank you! You've registered for a ${appointmentType} at ${clinicLocation} on ${preferredDate}. Our team will confirm your appointment shortly.`);
                
                // Reset form
                document.getElementById('clinic-location').value = '';
                document.getElementById('appointment-type').value = '';
                document.getElementById('preferred-date').value = '';
            });
            
            // Consultation booking
            const consultBtns = document.querySelectorAll('.consult-btn');
            
            consultBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorName = this.closest('.doctor-card').querySelector('.doctor-name').textContent;
                    alert(`Booking consultation with ${doctorName}... You'll be redirected to the scheduling system.`);
                });
            });
            
            // Set today's date as default for date input
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById('preferred-date').value = formattedDate;
            
            // If we have trimester data from PHP, update resources
            <?php if (!empty($trimester)): ?>
            updateResources('<?php echo $trimester; ?>');
            <?php endif; ?>
        });
    </script>
</body>
</html>