<?php
session_start();

if (!isset($_SESSION['first_name'])) {
    header("Location:../../login.php");
    exit();
}

$inactiveLimit = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactiveLimit) {
    session_unset();
    session_destroy();
    header("Location:../../login.php");
    exit();
}
$_SESSION['last_activity'] = time();

// Database connection
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get doctor data
$doctor_id = (int)$_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $doctor_id";
$result = mysqli_query($con, $query);
$doctor = mysqli_fetch_assoc($result);

// Fetch today's appointments with status and notes
$today = date('Y-m-d');
$appointments = [];
$appointmentQuery = "SELECT a.*, 
                    p.first_name AS patient_first_name, 
                    p.last_name AS patient_last_name,
                    p.email AS patient_email
                    FROM appointments a
                    JOIN users p ON a.patient_id = p.id
                    WHERE a.doctor_id = $doctor_id
                    AND a.appointment_date = '$today'
                    AND a.status IN ('pending', 'approved')
                    ORDER BY a.appointment_time ASC";
$appointmentResult = mysqli_query($con, $appointmentQuery);
if ($appointmentResult) {
    while ($row = mysqli_fetch_assoc($appointmentResult)) {
        $appointments[] = $row;
    }
}

// Fetch doctor's patients
$patients = [];
$patientQuery = "SELECT DISTINCT p.id, p.first_name, p.last_name, p.email
                FROM appointments a
                JOIN users p ON a.patient_id = p.id
                WHERE a.doctor_id = $doctor_id
                ORDER BY p.last_name ASC";
$patientResult = mysqli_query($con, $patientQuery);
if ($patientResult) {
    while ($row = mysqli_fetch_assoc($patientResult)) {
        $patients[] = $row;
    }
}

// Fetch pending prescriptions
$prescriptions = [];
$prescriptionQuery = "SELECT pr.*, 
                     p.first_name AS patient_first_name, 
                     p.last_name AS patient_last_name 
                     FROM prescriptions pr
                     JOIN users p ON pr.patient_id = p.id
                     WHERE pr.doctor_id = $doctor_id 
                     AND pr.status = 'pending'
                     ORDER BY pr.created_at DESC";
$prescriptionResult = mysqli_query($con, $prescriptionQuery);
if ($prescriptionResult) {
    while ($row = mysqli_fetch_assoc($prescriptionResult)) {
        $prescriptions[] = $row;
    }
}

// Fetch pending lab results
$labResults = [];
$labQuery = "SELECT lr.*, 
            p.first_name AS patient_first_name, 
            p.last_name AS patient_last_name 
            FROM lab_results lr
            JOIN users p ON lr.patient_id = p.id
            WHERE lr.doctor_id = $doctor_id 
            AND lr.status = 'pending'
            ORDER BY lr.test_date DESC";
$labResult = mysqli_query($con, $labQuery);
if ($labResult) {
    while ($row = mysqli_fetch_assoc($labResult)) {
        $labResults[] = $row;
    }
}

// Counts for dashboard stats
$appointmentCount = count($appointments);
$patientCount = count($patients);
$prescriptionCount = count($prescriptions);
$labResultCount = count($labResults);

// Get the requested page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Telemedicine Health</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.23.1.js"></script>
    <style>
        :root {
            --primary: #2a86ff;
            --primary-light: #e3f0ff;
            --primary-dark: #1a73e8;
            --secondary: #34a853;
            --secondary-light: #e6f4ea;
            --accent: #fbbc05;
            --accent-light: #fef7e0;
            --danger: #ea4335;
            --danger-light: #fce8e6;
            --success: #34a853;
            --light-bg: #f8f9fa;
            --text-dark: #202124;
            --text-medium: #5f6368;
            --text-light: #80868b;
            --border: #dadce0;
            --card-shadow: 0 1px 3px rgba(60,64,67,0.1), 0 4px 8px 3px rgba(60,64,67,0.15);
            --transition: all 0.3s ease;
            --doctor-primary: #1a73e8;
            --doctor-secondary: #5e97f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-logo {
            padding: 20px 15px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .sidebar-logo img {
            max-width: 150px;
            height: auto;
        }

        .user-profile {
            padding: 20px 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--doctor-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .user-info h3 {
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .user-info p {
            font-size: 0.85rem;
            color: var(--text-medium);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 15px 0;
        }

        .nav-section-title {
            padding: 10px 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-light);
            letter-spacing: 1px;
            font-weight: 500;
        }

        .nav-item {
            margin: 5px 15px;
            border-radius: 8px;
            overflow: hidden;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-medium);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border-radius: 8px;
        }

        .nav-item a:hover {
            background: rgba(42, 134, 255, 0.08);
            color: var(--doctor-primary);
        }

        .nav-item a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .nav-item.active a {
            background: rgba(26, 115, 232, 0.15);
            color: var(--doctor-primary);
            font-weight: 600;
        }

        .nav-divider {
            height: 1px;
            background: var(--border);
            margin: 10px 20px;
        }

        .submenu {
            padding-left: 40px;
            display: none;
        }

        .submenu .nav-item a {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .submenu .nav-item a i {
            font-size: 0.9rem;
        }

        .sidebar-signout {
            padding: 15px;
            border-top: 1px solid var(--border);
        }

        .btn-signout {
            width: 100%;
            padding: 10px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-medium);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-signout i {
            transition: transform 0.2s ease;
        }

        .btn-signout:hover {
            background: rgba(234, 67, 53, 0.08);
            color: var(--danger);
            border-color: rgba(234, 67, 53, 0.3);
        }

        .btn-signout:hover i {
            transform: translateX(-3px);
        }

        .sidebar-footer {
            padding: 15px;
            font-size: 0.75rem;
            color: var(--text-light);
            text-align: center;
            border-top: 1px solid var(--border);
        }

        .btn-signout:hover i {
            animation: pulse 0.5s ease;
        }

        @keyframes pulse {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(-5px); }
        }

        .btn-signout:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--danger);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: linear-gradient(135deg, #f5f7fa 0%, #e3e9f7 100%);
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .welcome-message h1 {
            font-size: 2.2rem;
            margin-bottom: 5px;
            color: var(--text-dark);
            font-weight: 700;
        }

        .welcome-message p {
            color: var(--text-medium);
            font-size: 1.1rem;
        }

        .date-display {
            background: white;
            padding: 12px 25px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            font-weight: 500;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-display i {
            color: var(--doctor-primary);
        }

        .section-title {
            font-size: 1.6rem;
            margin: 40px 0 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
            color: var(--text-dark);
            font-weight: 700;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: var(--doctor-primary);
            border-radius: 3px;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            position: relative;
            padding-bottom: 10px;
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--doctor-primary);
            border-radius: 3px;
        }

        /* Appointment Card Styles */
        .appointments-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .appointment-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border-left: 4px solid var(--doctor-primary);
        }

        .appointment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .appointment-time {
            font-weight: 600;
            color: var(--doctor-primary);
            font-size: 1.1rem;
        }

        .appointment-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending {
            background: rgba(251, 188, 5, 0.1);
            color: #fbbc05;
        }

        .status-approved {
            background: rgba(52, 168, 83, 0.1);
            color: #34a853;
        }

        .status-active {
            background: rgba(66, 133, 244, 0.1);
            color: #4285f4;
        }

        .status-completed {
            background: rgba(66, 133, 244, 0.1);
            color: #4285f4;
        }

        .status-cancelled {
            background: rgba(234, 67, 53, 0.1);
            color: #ea4335;
        }

        .appointment-patient {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .patient-avatar-small {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e3f0ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--doctor-primary);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .patient-info-small h4 {
            margin-bottom: 5px;
        }

        .patient-info-small p {
            color: #5f6368;
            font-size: 0.9rem;
        }

        .appointment-actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
        }

        .appointment-actions .btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success {
            background: var(--success);
            color: white;
            border: none;
        }

        .btn-success:hover {
            background: #2d9046;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f1f3f4;
            color: var(--text-dark);
            border: none;
        }

        .btn-secondary:hover {
            background: #e8eaed;
        }

        .btn-primary {
            background: var(--doctor-primary);
            color: white;
            border: none;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--doctor-secondary);
            transform: translateY(-2px);
        }

        .btn-danger:hover {
            background: #d13224;
            transform: translateY(-2px);
        }

        .no-appointments {
            text-align: center;
            padding: 40px;
            color: #5f6368;
            grid-column: 1 / -1;
        }

        /* Doctor Stats Grid */
        .doctor-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .doctor-stat-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-left: 4px solid var(--doctor-primary);
        }

        .doctor-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--doctor-primary);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: var(--text-medium);
            margin-bottom: 15px;
        }

        .stat-icon {
            align-self: flex-end;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: rgba(26, 115, 232, 0.1);
            color: var(--doctor-primary);
        }

        /* Patient List Styles */
        .patient-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .patient-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border-left: 4px solid var(--doctor-secondary);
        }

        .patient-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .patient-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .patient-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--doctor-primary);
            font-weight: bold;
            font-size: 1.5rem;
        }

        .patient-info h4 {
            margin-bottom: 5px;
        }

        .patient-info p {
            color: #5f6368;
            font-size: 0.9rem;
        }

        .patient-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            color: var(--text-medium);
        }

        .patient-actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
        }

        /* Prescription Card Styles */
        .prescription-card, .lab-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.3s ease;
        }

        .prescription-card:hover, .lab-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .prescription-header, .lab-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .prescription-title, .lab-title {
            font-weight: 600;
            color: var(--text-dark);
        }

        .prescription-date, .lab-date {
            color: var(--text-medium);
            font-size: 0.9rem;
        }

        .prescription-content, .lab-content {
            margin-bottom: 10px;
            color: var(--text-medium);
        }

        .prescription-status, .lab-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background: rgba(251, 188, 5, 0.1);
            color: var(--accent);
        }

        .status-completed {
            background: rgba(52, 168, 83, 0.1);
            color: var(--secondary);
        }

        /* Notification Styles */
        .app-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            z-index: 1000;
            transform: translateX(0);
            opacity: 1;
            transition: all 0.3s ease;
        }

        .app-notification.success {
            background: #4CAF50;
            border-left: 4px solid #388E3C;
        }

        .app-notification.error {
            background: #F44336;
            border-left: 4px solid #D32F2F;
        }

        .app-notification.info {
            background: #2196F3;
            border-left: 4px solid #0b7dda;
        }

        .app-notification .notification-content {
            flex: 1;
            padding-right: 20px;
        }

        .app-notification .close-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0 5px;
            line-height: 1;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .app-notification {
            animation: slideIn 0.3s ease forwards;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar-logo img {
                max-width: 40px;
            }
            
            .user-profile, .nav-section-title, .sidebar-footer, .nav-item span {
                display: none;
            }
            
            .nav-item {
                margin: 5px;
                text-align: center;
            }
            
            .nav-item a {
                justify-content: center;
                padding: 15px 5px;
            }
            
            .nav-item a i {
                margin: 0;
                font-size: 1.3rem;
            }
            
            /* Mobile sidebar icon hover effect */
            .nav-item a:hover i {
                transform: scale(1.2);
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .date-display {
                width: 100%;
            }
            
            .doctor-stats-grid {
                grid-template-columns: 1fr;
            }
            
            .appointments-container, .patient-list {
                grid-template-columns: 1fr;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .doctor-stat-card, .appointment-card, .patient-card, .card {
            animation: fadeIn 0.6s ease-out;
        }

        /* Video call styles */
        .video-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
            background: #1a1e25;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .video-header {
            background: #2a2f3b;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #3a3f4b;
        }

        .video-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .video-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ea4335;
        }

        .status-indicator.connected {
            background: #34a853;
        }

        .video-content {
            flex: 1;
            display: flex;
            position: relative;
        }

        .remote-video-container {
            flex: 1;
            background: #0f1217;
            position: relative;
        }

        .local-video-container {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 180px;
            height: 135px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
        }

        .video-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #5f6b82;
            text-align: center;
            padding: 20px;
            z-index: 5;
        }

        .video-placeholder i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            color: #2a86ff;
        }

        .video-placeholder h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #e3f0ff;
        }

        .video-placeholder p {
            max-width: 500px;
            line-height: 1.6;
        }

        .video-controls {
            background: #2a2f3b;
            padding: 15px 20px;
            display: flex;
            justify-content: center;
            gap: 25px;
            border-top: 1px solid #3a3f4b;
        }

        .control-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #3a3f4b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            color: white;
            font-size: 1.4rem;
        }

        .control-btn:hover {
            transform: scale(1.05);
            background: #4a4f5b;
        }

        .control-btn.end-call {
            background: #ea4335;
        }

        .control-btn.end-call:hover {
            background: #d13224;
        }

        .control-btn.active {
            background: #2a86ff;
        }

        .timer-display {
            font-size: 0.9rem;
            color: #e3f0ff;
            background: rgba(0,0,0,0.3);
            padding: 5px 10px;
            border-radius: 20px;
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 20;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--doctor-primary);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2);
        }

        /* Loading indicator */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .loading i {
            font-size: 2rem;
            color: var(--doctor-primary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .modal-header {
            padding: 20px;
            background: var(--doctor-primary);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 25px;
            color: var(--text-medium);
        }

        .modal-footer {
            padding: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            border-top: 1px solid var(--border);
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--doctor-primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--doctor-secondary);
        }

        .btn-secondary {
            background: #f1f3f4;
            color: var(--text-dark);
            border: none;
        }

        .btn-secondary:hover {
            background: #e8eaed;
        }

        /* Status Update Modal */
        .status-update-modal {
            max-width: 600px;
        }

        .status-update-modal .modal-body {
            padding: 30px;
        }

        .status-update-modal .form-group {
            margin-bottom: 25px;
        }

        .status-update-modal .form-control {
            padding: 12px;
        }

        .status-update-modal .status-history {
            max-height: 200px;
            overflow-y: auto;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .status-history-item {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 0.9rem;
        }

        .status-history-item:last-child {
            border-bottom: none;
        }

        /* Nav item icon hover animation */
        .nav-item a:hover i {
            transform: translateY(-3px);
        }

        /* Stat icon animation */
        .doctor-stat-card:hover .stat-icon {
            transform: scale(1.1);
            background: rgba(26, 115, 232, 0.2);
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCAxNDAgNDAiPjxwYXRoIGQ9Ik0xMCAxMEg0MFYzMEgxMFoiIGZpbGw9IiMyQTg2RkYiLz48cGF0aCBkPSJNNDAgMTBINzBWMzBINDBaIiBmaWxsPSIjMUEzQTVGIi8+PHBhdGggZD0iTTcwIDEwSDEwMFYzMEg3MFoiIGZpbGw9IiMyQTg2RkYiLz48cGF0aCBkPSJNMTQwIDEwSDExMVYzMEgxNDBaIiBmaWxsPSIjMUEzQTVGIi8+PHRleHQgeD0iNDAiIHk9IjM1IiBmb250LWZhbWlseT0iUG9wcGlucyIgZm9udC1zaXplPSIxNiIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0iIzFBM0E1RiI+TElWQTwvdGV4dD48L3N2Zz4=" alt="Telemedicine Logo">
        </div>
        
        <div class="user-profile">
            <div class="user-avatar"><?= substr($doctor['first_name'], 0, 1) ?></div>
            <div class="user-info">
                <h3>Dr. <?= $doctor['first_name'] ?></h3>
                <p><?= $doctor['specialty'] ?></p>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section-title">Main Navigation</div>
            <ul>
                <li class="nav-item"><a href="#" data-content="dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li class="nav-item"><a href="#" data-content="appointments"><i class="far fa-calendar-alt"></i> <span>Appointments</span></a></li>
                <li class="nav-item"><a href="#" data-content="schedule"><i class="far fa-calendar-check"></i> <span>Schedule</span></a></li>
                <li class="nav-item"><a href="#" data-content="patients"><i class="fas fa-user-injured"></i> <span>Patients</span></a></li>
            </ul>
            
            <div class="nav-section-title">Health Services</div>
            <ul>
                <li class="nav-item"><a href="#" data-content="labs"><i class="fas fa-flask"></i> <span>Lab Results</span></a></li>
                <li class="nav-item"><a href="#" data-content="prescriptions"><i class="fas fa-file-prescription"></i> <span>Prescriptions</span></a></li>
                <li class="nav-item"><a href="#" data-content="records"><i class="fas fa-notes-medical"></i> <span>Health Records</span></a></li>
                <li class="nav-item"><a href="#" data-content="video"><i class="fas fa-video"></i> <span>Video Consult</span></a></li>
            </ul>
            
            <div class="nav-section-title">Resources</div>
            <ul>
                <li class="nav-item"><a href="#" data-content="resources"><i class="fas fa-microscope"></i> <span>Medical Resources</span></a></li>
                <li class="nav-item"><a href="#" data-content="medicines"><i class="fas fa-pills"></i> <span>Medicine Database</span></a></li>
            </ul>
            
            <div class="nav-divider"></div>
            
            <div class="nav-section-title">Preferences</div>
            <ul>
                <li class="nav-item"><a href="#" data-content="settings"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li class="nav-item"><a href="#" data-content="support"><i class="fas fa-question-circle"></i> <span>Support</span></a></li>
            </ul>
        </nav>
        
        <div class="sidebar-signout">
            <button class="btn-signout" id="signOutBtn"><i class="fas fa-sign-out-alt"></i> Sign Out</button>
        </div>
        
        <div class="sidebar-footer">
            <p>Copyright © <?= date('Y') ?> Telemedicine Health. All rights reserved.</p>
        </div>
    </aside>

    <!-- Sign Out Modal -->
    <div class="modal" id="confirm-sign-out">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Sign Out</h3>
                <button class="close-modal">×</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to sign out?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="confirm-sign-out-no">Cancel</button>
                <button class="btn btn-primary" id="confirm-sign-out-yes">Sign Out</button>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal" id="status-update-modal">
        <div class="modal-content status-update-modal">
            <div class="modal-header">
                <h3 class="modal-title">Update Appointment Status</h3>
                <button class="close-modal">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Current Status</label>
                    <input type="text" class="form-control" id="current-status" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">New Status</label>
                    <select class="form-control" id="new-status">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" id="status-notes" rows="4"></textarea>
                </div>
                <div class="status-history" id="status-history">
                    <p>Loading status history...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="status-cancel">Cancel</button>
                <button class="btn btn-primary" id="status-update">Update Status</button>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="main-content">
        <!-- Content will be loaded dynamically here -->
    </div>
    
    <!-- Notification Container -->
    <div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>

    <script>
    // Pass PHP data to JavaScript
    const doctorData = {
        id: <?= $doctor_id ?>,
        firstName: "<?= $doctor['first_name'] ?>",
        specialty: "<?= $doctor['specialty'] ?>",
        appointments: <?php echo json_encode($appointments); ?>,
        patients: <?php echo json_encode($patients); ?>,
        prescriptions: <?php echo json_encode($prescriptions); ?>,
        labResults: <?php echo json_encode($labResults); ?>,
        counts: {
            appointments: <?= $appointmentCount ?>,
            patients: <?= $patientCount ?>,
            prescriptions: <?= $prescriptionCount ?>,
            labResults: <?= $labResultCount ?>
        }
    };
    
    // Global variables for video call
    let agoraClient = null;
    let localTracks = {
        audioTrack: null,
        videoTrack: null,
        screenTrack: null
    };
    let remoteUsers = {};
    let currentAppointment = null;
    let callTimer = null;
    let callDuration = 0;
    
    // Main initialization function
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize page
        if (window.initDoctorPage) {
            window.initDoctorPage();
        }
        
        // SPA navigation
        document.querySelectorAll('.sidebar-nav .nav-item a').forEach(link => {
            link.addEventListener('click', function(e) {
                const parentItem = this.parentElement;
                
                // Handle submenu toggle
                if (parentItem.classList.contains('has-submenu')) {
                    e.preventDefault();
                    const submenu = parentItem.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                    }
                    return;
                }
                
                e.preventDefault();
                const page = this.getAttribute('data-content');
                
                // Update active state
                document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                parentItem.classList.add('active');
                
                // Load content
                loadMainContent(page);
            });
        });
        
        // Sign out functionality
        const signOutBtn = document.getElementById('signOutBtn');
        const confirmModal = document.getElementById('confirm-sign-out');
        const closeModalBtn = document.querySelector('.close-modal');
        
        if (signOutBtn) {
            signOutBtn.addEventListener('click', function() {
                confirmModal.style.display = 'flex';
            });
        }
        
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                confirmModal.style.display = 'none';
            });
        }
        
        document.getElementById('confirm-sign-out-yes')?.addEventListener('click', function() {
            window.location.href = '../../login.php';
        });
        
        document.getElementById('confirm-sign-out-no')?.addEventListener('click', function() {
            confirmModal.style.display = 'none';
        });
        
        // Status modal event listeners
        const statusModal = document.getElementById('status-update-modal');
        const statusCancelBtn = document.getElementById('status-cancel');
        const statusUpdateBtn = document.getElementById('status-update');

        if (statusCancelBtn) {
            statusCancelBtn.addEventListener('click', () => {
                statusModal.style.display = 'none';
            });
        }

        if (statusUpdateBtn) {
            statusUpdateBtn.addEventListener('click', updateAppointmentStatus);
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === confirmModal) {
                confirmModal.style.display = 'none';
            }
            if (event.target === statusModal) {
                statusModal.style.display = 'none';
            }
        });
        
        // Initialize Dashboard as active
        const dashboardLink = document.querySelector('.nav-item a[data-content="dashboard"]');
        if (dashboardLink) {
            dashboardLink.parentNode.classList.add('active');
        }
        
        // Load initial page content
        const initialPage = "<?= $page ?>";
        loadMainContent(initialPage);
    });

    function loadMainContent(page) {
        const mainContent = document.getElementById('main-content');
        
        // Show loading indicator
        mainContent.innerHTML = `
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <div class="welcome-message">
                        <h1>Loading...</h1>
                        <p>Please wait while we load your content</p>
                    </div>
                    <div class="date-display">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading</span>
                    </div>
                </div>
            </div>
        `;
        
        // Simulate content loading
        setTimeout(() => {
            // Inject content based on page
            mainContent.innerHTML = getPageContent(page);
            
            // Update URL without reloading page
            window.history.pushState({ page }, '', `?page=${page}`);
            
            // Re-init page scripts
            if (window.initDoctorPage) {
                window.initDoctorPage();
            }
            
            // Initialize specific page components
            if (page === 'appointments') {
                loadAppointments('appointmentsContainer');
            }
            else if (page === 'patients') {
                loadPatients('patientsContainer');
            }
            else if (page === 'labs') {
                initLabsPage();
            }
            else if (page === 'video') {
                initVideoPage();
            }
        }, 500);
    }

    function getPageContent(page) {
        const today = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        let content = '';
        
        switch(page) {
            case 'dashboard':
                content = `
                    <div class="dashboard-container">
                        <div class="dashboard-header">
                            <div class="welcome-message">
                                <h1>Welcome Back, Dr. ${doctorData.firstName}</h1>
                                <p>Your schedule and patient management dashboard</p>
                            </div>
                            <div class="date-display">
                                <i class="fas fa-calendar"></i>
                                <span>${today}</span>
                            </div>
                        </div>
                        
                        <!-- Statistics Cards -->
                        <div class="doctor-stats-grid">
                            <div class="doctor-stat-card">
                                <div class="stat-number">${doctorData.counts.appointments}</div>
                                <div class="stat-label">Scheduled Appointments Today</div>
                                <div class="stat-icon">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            
                            <div class="doctor-stat-card" onclick="loadPage('appointments')">
                                <div class="stat-number">${doctorData.counts.patients}</div>
                                <div class="stat-label">Active Patients</div>
                                <div class="stat-icon">
                                    <i class="fas fa-user-injured"></i>
                                </div>
                            </div>
                            
                            <div class="doctor-stat-card">
                                <div class="stat-number">${doctorData.counts.prescriptions}</div>
                                <div class="stat-label">Pending Prescriptions</div>
                                <div class="stat-icon">
                                    <i class="fas fa-file-prescription"></i>
                                </div>
                            </div>
                            
                            <div class="doctor-stat-card">
                                <div class="stat-number">${doctorData.counts.labResults}</div>
                                <div class="stat-label">Lab Results to Review</div>
                                <div class="stat-icon">
                                    <i class="fas fa-microscope"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Today's Appointments Section -->
                        <div class="section-title">Today's Appointments</div>
                        <div id="appointmentsContainer">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i> Loading appointments...
                            </div>
                        </div>
                        
                        <!-- Pending Actions Section -->
                        <div class="section-title">Pending Actions</div>
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Prescriptions to Approve</div>
                            </div>
                            <div class="card-content" id="prescriptionsContainer">
                                <div class="loading">
                                    <i class="fas fa-spinner fa-spin"></i> Loading prescriptions...
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lab Results Section -->
                        <div class="section-title">Lab Results to Review</div>
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Pending Lab Reports</div>
                            </div>
                            <div class="card-content" id="labResultsContainer">
                                <div class="loading">
                                    <i class="fas fa-spinner fa-spin"></i> Loading lab results...
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                break;
                
            case 'appointments':
                content = `
                    <div class="dashboard-container">
                        <div class="dashboard-header">
                            <div class="welcome-message">
                                <h1>Appointment Approvals</h1>
                                <p>Review and manage your pending appointments</p>
                            </div>
                            <div class="date-display">
                                <i class="fas fa-calendar"></i>
                                <span>${today}</span>
                            </div>
                        </div>
                        
                        <div class="section-title">Pending & Approved Appointments</div>
                        <div class="card">
                            <div class="card-content">
                                <div id="appointmentsContainer">
                                    <div class="loading">
                                        <i class="fas fa-spinner fa-spin"></i> Loading appointments...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                break;
                
                case 'patients':
    content = `
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div class="welcome-message">
                    <h1>Patient Management</h1>
                    <p>View and manage your patients</p>
                </div>
                <div class="date-display">
                    <i class="fas fa-user-injured"></i>
                    <span>${today}</span>
                </div>
            </div>
            
            <div class="section-title">Your Patients</div>
            <div id="patientsContainer">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading patients...
                </div>
            </div>
            
            <div class="section-title">Add New Patient</div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Patient Assignment</div>
                </div>
                <div class="card-content">
                    <form id="patientForm">
                        <div class="form-group">
                            <label class="form-label">Select Patient</label>
                            <select class="form-control" id="patientSelect">
                                <option value="">Select a patient</option>
                                ${doctorData.availablePatients ? doctorData.availablePatients.map(patient => `
                                    <option value="${patient.id}">${patient.first_name} ${patient.last_name} (${patient.email})</option>
                                `).join('') : ''}
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" onclick="addPatient()">Add Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    break;
                
            case 'labs':
                content = `
                    <div class="dashboard-container">
                        <div class="dashboard-header">
                            <div class="welcome-message">
                                <h1>Lab Results Management</h1>
                                <p>Review and manage laboratory test results</p>
                            </div>
                            <div class="date-display">
                                <i class="fas fa-flask"></i>
                                <span>${today}</span>
                            </div>
                        </div>
                        
                        <div class="section-title">Pending Lab Results</div>
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Lab Reports Requiring Review</div>
                            </div>
                            <div class="card-content" id="labResultsContainer">
                                <div class="loading">
                                    <i class="fas fa-spinner fa-spin"></i> Loading lab results...
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-title">Upload New Results</div>
                        <div class="card">
                            <div class="card-content">
                                <form id="labUploadForm">
                                    <div class="form-group">
                                        <label class="form-label">Patient</label>
                                        <select class="form-control" id="labPatient">
                                            <option value="">Select patient</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Test Type</label>
                                        <input type="text" class="form-control" id="labTestType">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Test Date</label>
                                        <input type="date" class="form-control" id="labTestDate">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Results File</label>
                                        <input type="file" class="form-control" id="labResultFile">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Interpretation Notes</label>
                                        <textarea class="form-control" id="labInterpretation" rows="4"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary" onclick="uploadLabResult()">Upload Results</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `;
                break;
                
            case 'video':
                content = `
                    <div class="dashboard-container">
                        <div class="dashboard-header">
                            <div class="welcome-message">
                                <h1>Video Consultation</h1>
                                <p>Connect with patients through video calls</p>
                            </div>
                            <div class="date-display">
                                <i class="fas fa-video"></i>
                                <span>${today}</span>
                            </div>
                        </div>
                        
                        <div class="section-title">Active Consultations</div>
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Video Conference</div>
                            </div>
                            <div class="card-content">
                                <div class="video-container">
                                    <div class="video-header">
                                        <div class="video-title">Telemedicine Consultation</div>
                                        <div class="video-status">
                                            <div class="status-indicator" id="connection-status"></div>
                                            <span id="connection-text">Disconnected</span>
                                        </div>
                                    </div>
                                    
                                    <div class="video-content">
                                        <div id="timer-display" class="timer-display">00:00</div>
                                        <div class="remote-video-container" id="remote-video">
                                            <div class="video-placeholder" id="remote-placeholder">
                                                <i class="fas fa-user-injured"></i>
                                                <h3>Waiting for Patient to Join</h3>
                                                <p>Your patient will appear here once they join the consultation</p>
                                            </div>
                                        </div>
                                        
                                        <div class="local-video-container" id="local-video">
                                            <div class="video-placeholder" id="local-placeholder">
                                                <i class="fas fa-user-md"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="video-controls">
                                        <button class="control-btn" id="mic-toggle">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                        <button class="control-btn" id="camera-toggle">
                                            <i class="fas fa-video"></i>
                                        </button>
                                        <button class="control-btn" id="screen-share">
                                            <i class="fas fa-desktop"></i>
                                        </button>
                                        <button class="control-btn end-call" id="end-call">
                                            <i class="fas fa-phone-slash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-title">Scheduled Appointments</div>
                        <div id="appointmentsContainer">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i> Loading appointments...
                            </div>
                        </div>
                    </div>
                `;
                break;
                
            default:
                content = getPageContent('dashboard');
        }
        
        return content;
    }
    
    /* ================ DASHBOARD PAGE ================ */
    function loadAppointments(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading appointments...</div>';
        
        fetch('../backend/get_doctor_appointments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                doctor_id: doctorData.id,
                status: ['pending', 'approved']
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.appointments.length > 0) {
                let appointmentsHTML = '<div class="appointments-container">';
                
                data.appointments.forEach(appt => {
                    const time = new Date(`2000-01-01 ${appt.appointment_time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const date = new Date(appt.appointment_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const statusClass = `status-${appt.status}`;
                    
                    appointmentsHTML += `
                        <div class="appointment-card" data-appointment-id="${appt.id}">
                            <div class="appointment-header">
                                <div class="appointment-time">${date} at ${time}</div>
                                <div class="appointment-status ${statusClass}">${appt.status.charAt(0).toUpperCase() + appt.status.slice(1)}</div>
                            </div>
                            <div class="appointment-patient">
                                <div class="patient-avatar-small">${appt.patient_first_name.charAt(0)}</div>
                                <div class="patient-info-small">
                                    <h4>${appt.patient_first_name} ${appt.patient_last_name}</h4>
                                    <p>Email: ${appt.patient_email}</p>
                                    <p>Reason: ${appt.reason}</p>
                                    <p>Notes: ${appt.notes || 'None'}</p>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                ${appt.status === 'pending' ? `
                                    <button class="btn btn-success" onclick="approveAppointment(${appt.id})">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                ` : ''}
                                <button class="btn btn-primary" onclick="startCall(${appt.id}, '${appt.patient_first_name}')">
                                    <i class="fas fa-video"></i> Start Video Call
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                appointmentsHTML += '</div>';
                container.innerHTML = appointmentsHTML;
            } else {
                container.innerHTML = `
                    <div class="no-appointments">
                        <i class="far fa-calendar-check fa-3x" style="color: #5f6368; margin-bottom: 15px;"></i>
                        <h3>No Appointments</h3>
                        <p>You have no pending or approved appointments</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            showNotification('Error loading appointments: ' + error.message, 'error');
            container.innerHTML = `
                <div class="no-appointments">
                    <i class="fas fa-exclamation-circle fa-3x" style="color: #ea4335; margin-bottom: 15px;"></i>
                    <h3>Error Loading Appointments</h3>
                    <p>Unable to load appointments at this time</p>
                </div>
            `;
        });
    }
    
    function approveAppointment(appointmentId) {
        fetch('../backend/approve_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                doctor_id: doctorData.id,
                status: 'approved',
                notes: 'Appointment approved by doctor'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const appointmentCard = document.querySelector(`.appointment-card[data-appointment-id="${appointmentId}"]`);
                if (appointmentCard) {
                    const statusElement = appointmentCard.querySelector('.appointment-status');
                    statusElement.className = 'appointment-status status-approved';
                    statusElement.textContent = 'Approved';
                    
                    const actionsElement = appointmentCard.querySelector('.appointment-actions');
                    actionsElement.innerHTML = `
                        <button class="btn btn-primary" onclick="startCall(${appointmentId}, '${doctorData.appointments.find(appt => appt.id === appointmentId)?.patient_first_name || ''}')">
                            <i class="fas fa-video"></i> Start Video Call
                        </button>
                    `;
                    
                    const patientInfo = appointmentCard.querySelector('.patient-info-small');
                    const notesElement = patientInfo.querySelector('p:last-child');
                    notesElement.textContent = 'Notes: Appointment approved by doctor';
                }
                showNotification('Appointment approved successfully', 'success');
                
                // Update local data
                const appointment = doctorData.appointments.find(appt => appt.id === appointmentId);
                if (appointment) {
                    appointment.status = 'approved';
                    appointment.notes = 'Appointment approved by doctor';
                }
            } else {
                showNotification('Failed to approve appointment: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error approving appointment: ' + error.message, 'error');
        });
    }
    
    function loadPrescriptions(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading prescriptions...</div>';
        
        setTimeout(() => {
            if (doctorData.prescriptions.length > 0) {
                let prescriptionsHTML = '';
                
                doctorData.prescriptions.forEach(prescription => {
                    const date = new Date(prescription.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    
                    prescriptionsHTML += `
                        <div class="prescription-card">
                            <div class="prescription-header">
                                <div class="prescription-title">Prescription #PRX-${prescription.id}</div>
                                <div class="prescription-date">${date}</div>
                            </div>
                            <div class="prescription-content">
                                <p><strong>Patient:</strong> ${prescription.patient_first_name} ${prescription.patient_last_name}</p>
                                <p><strong>Medication:</strong> ${prescription.medication_name}</p>
                                <p><strong>Dosage:</strong> ${prescription.dosage}</p>
                            </div>
                            <div class="prescription-actions">
                                <button class="btn btn-secondary" onclick="viewPrescriptionDetails(${prescription.id})">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-primary" onclick="approvePrescription(${prescription.id})">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </div>
                            <div class="prescription-status status-pending">Pending Approval</div>
                        </div>
                    `;
                });
                
                container.innerHTML = prescriptionsHTML;
            } else {
                container.innerHTML = `
                    <div class="no-appointments">
                        <i class="fas fa-check-circle fa-3x" style="color: #34a853; margin-bottom: 15px;"></i>
                        <p>No pending prescriptions</p>
                    </div>
                `;
            }
        }, 800);
    }
    
    function loadLabResults(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading lab results...</div>';
        
        setTimeout(() => {
            if (doctorData.labResults.length > 0) {
                let labResultsHTML = '';
                
                doctorData.labResults.forEach(lab => {
                    const date = new Date(lab.test_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    
                    labResultsHTML += `
                        <div class="lab-card" data-lab-id="${lab.id}">
                            <div class="lab-header">
                                <div class="lab-title">${lab.test_type}</div>
                                <div class="lab-date">${date}</div>
                            </div>
                            <div class="lab-content">
                                <p><strong>Patient:</strong> ${lab.patient_first_name} ${lab.patient_last_name}</p>
                                <p><strong>Type:</strong> ${lab.test_type}</p>
                                <div class="result-details" style="margin-top: 10px; display: none;">
                                    <h4>Result Details:</h4>
                                    <pre>${lab.result_details}</pre>
                                </div>
                            </div>
                            <div class="lab-actions" style="margin-top: 15px;">
                                <button class="btn btn-secondary toggle-details">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="btn btn-primary mark-reviewed">
                                    <i class="fas fa-check"></i> Mark as Reviewed
                                </button>
                            </div>
                            <div class="lab-status status-pending">Pending Review</div>
                        </div>
                    `;
                });
                
                container.innerHTML = labResultsHTML;
                
                // Add event listeners for lab actions
                document.querySelectorAll('.toggle-details').forEach(button => {
                    button.addEventListener('click', function() {
                        const details = this.closest('.lab-card').querySelector('.result-details');
                        details.style.display = details.style.display === 'none' ? 'block' : 'none';
                        this.innerHTML = details.style.display === 'none' 
                            ? '<i class="fas fa-eye"></i> View Details' 
                            : '<i class="fas fa-eye-slash"></i> Hide Details';
                    });
                });
                
                document.querySelectorAll('.mark-reviewed').forEach(button => {
                    button.addEventListener('click', function() {
                        const labCard = this.closest('.lab-card');
                        const labId = labCard.dataset.labId;
                        markLabReviewed(labId, labCard);
                    });
                });
            } else {
                container.innerHTML = `
                    <div class="no-appointments">
                        <i class="fas fa-check-circle fa-3x" style="color: #34a853; margin-bottom: 15px;"></i>
                        <p>No pending lab results</p>
                    </div>
                `;
            }
        }, 800);
    }
    
    function loadPatients(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading patients...</div>';
        
        setTimeout(() => {
            if (doctorData.patients.length > 0) {
                let patientsHTML = '';
                
                doctorData.patients.forEach(patient => {
                    patientsHTML += `
                        <div class="patient-card">
                            <div class="patient-header">
                                <div class="patient-avatar">${patient.first_name.charAt(0)}</div>
                                <div class="patient-info">
                                    <h4>${patient.first_name} ${patient.last_name}</h4>
                                    <p>${patient.email}</p>
                                </div>
                            </div>
                            <div class="patient-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Last visit: 2 weeks ago</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-file-medical"></i>
                                    <span>5 records</span>
                                </div>
                            </div>
                            <div class="patient-actions">
                                <button class="btn btn-secondary" onclick="viewPatientDetails(${patient.id})">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-primary" onclick="scheduleAppointment(${patient.id})">
                                    <i class="fas fa-calendar-plus"></i> Schedule
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = `<div class="patient-list">${patientsHTML}</div>`;
            } else {
                container.innerHTML = `
                    <div class="no-appointments">
                        <i class="fas fa-user-slash fa-3x" style="color: #5f6368; margin-bottom: 15px;"></i>
                        <h3>No Patients Found</h3>
                        <p>You haven't added any patients yet</p>
                    </div>
                `;
            }
        }, 800);
    }
    
    /* ================ STATUS MANAGEMENT ================ */
    let currentAppointmentId = null;

    function openStatusModal(appointmentId, currentStatus) {
        currentAppointmentId = appointmentId;
        const modal = document.getElementById('status-update-modal');
        const currentStatusInput = document.getElementById('current-status');
        const newStatusSelect = document.getElementById('new-status');
        const statusNotes = document.getElementById('status-notes');
        const statusHistory = document.getElementById('status-history');

        currentStatusInput.value = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
        newStatusSelect.value = currentStatus;
        statusNotes.value = '';
        
        // Load status history
        loadStatusHistory(appointmentId, statusHistory);

        modal.style.display = 'flex';
    }

            function loadStatusHistory(appointmentId, container) {
        fetch('../backend/get_status_history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.history.length > 0) {
                let historyHTML = '';
                data.history.forEach(entry => {
                    const date = new Date(entry.updated_at).toLocaleString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    historyHTML += `
                        <div class="status-history-item">
                            <p><strong>Status:</strong> ${entry.status.charAt(0).toUpperCase() + entry.status.slice(1)}</p>
                            <p><strong>Updated:</strong> ${date}</p>
                            <p><strong>Notes:</strong> ${entry.notes || 'None'}</p>
                        </div>
                    `;
                });
                container.innerHTML = historyHTML;
            } else {
                container.innerHTML = '<p>No status history available.</p>';
            }
        })
        .catch(error => {
            container.innerHTML = '<p>Error loading status history.</p>';
            showNotification('Error loading status history: ' + error.message, 'error');
        });
    }

    function updateAppointmentStatus() {
        const newStatus = document.getElementById('new-status').value;
        const notes = document.getElementById('status-notes').value;
        const modal = document.getElementById('status-update-modal');

        fetch('../backend/update_appointment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: currentAppointmentId,
                doctor_id: doctorData.id,
                status: newStatus,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Appointment status updated successfully', 'success');
                modal.style.display = 'none';
                
                // Update local data
                const appointment = doctorData.appointments.find(appt => appt.id === currentAppointmentId);
                if (appointment) {
                    appointment.status = newStatus;
                    appointment.notes = notes;
                }
                
                // Reload appointments
                loadAppointments('appointmentsContainer');
            } else {
                showNotification('Failed to update status: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error updating status: ' + error.message, 'error');
        });
    }

    /* ================ VIDEO CALL FUNCTIONALITY ================ */
    function initVideoPage() {
        // Initialize Agora client
        if (!agoraClient) {
            agoraClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
        }

        // Initialize event listeners for video controls
        const micToggle = document.getElementById('mic-toggle');
        const cameraToggle = document.getElementById('camera-toggle');
        const screenShareBtn = document.getElementById('screen-share');
        const endCallBtn = document.getElementById('end-call');

        if (micToggle) {
            micToggle.addEventListener('click', toggleMic);
        }
        if (cameraToggle) {
            cameraToggle.addEventListener('click', toggleCamera);
        }
        if (screenShareBtn) {
            screenShareBtn.addEventListener('click', toggleScreenShare);
        }
        if (endCallBtn) {
            endCallBtn.addEventListener('click', endCall);
        }

        // Load appointments for video page
        loadAppointments('appointmentsContainer');
    }

    async function startCall(appointmentId, patientName) {
        if (!agoraClient) {
            agoraClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
        }

        currentAppointment = doctorData.appointments.find(appt => appt.id === appointmentId);
        if (!currentAppointment) {
            showNotification('Appointment not found', 'error');
            return;
        }

        try {
            // Get Agora token
            const response = await fetch('../backend/generate_agora_token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    appointment_id: appointmentId,
                    user_id: doctorData.id,
                    role: 'publisher'
                })
            });
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message);
            }

            const { token, channel } = data;

            // Join channel
            await agoraClient.join('YOUR_AGORA_APP_ID', channel, token, doctorData.id);

            // Create and publish local tracks
            localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();
            
            await agoraClient.publish([localTracks.audioTrack, localTracks.videoTrack]);

            // Play local video
            const localVideoContainer = document.getElementById('local-video');
            localVideoContainer.innerHTML = '';
            localTracks.videoTrack.play(localVideoContainer);
            
            // Update UI
            document.getElementById('connection-status').classList.add('connected');
            document.getElementById('connection-text').textContent = `Connected with ${patientName}`;
            document.getElementById('remote-placeholder').style.display = 'none';

            // Start call timer
            startCallTimer();

            // Setup event listeners for remote users
            agoraClient.on('user-published', handleUserPublished);
            agoraClient.on('user-unpublished', handleUserUnpublished);
            agoraClient.on('user-left', handleUserLeft);

            showNotification(`Video call started with ${patientName}`, 'success');
            
            // Update appointment status to active
            updateAppointmentStatusToActive(appointmentId);
        } catch (error) {
            showNotification('Error starting video call: ' + error.message, 'error');
        }
    }

    async function handleUserPublished(user, mediaType) {
        await agoraClient.subscribe(user, mediaType);
        
        if (mediaType === 'video') {
            const remoteVideoContainer = document.getElementById('remote-video');
            remoteVideoContainer.innerHTML = '';
            user.videoTrack.play(remoteVideoContainer);
        }
        
        if (mediaType === 'audio') {
            user.audioTrack.play();
        }
        
        remoteUsers[user.uid] = user;
    }

    function handleUserUnpublished(user, mediaType) {
        if (mediaType === 'video') {
            const remoteVideoContainer = document.getElementById('remote-video');
            remoteVideoContainer.innerHTML = `
                <div class="video-placeholder" id="remote-placeholder">
                    <i class="fas fa-user-injured"></i>
                    <h3>Waiting for Patient to Join</h3>
                    <p>Your patient will appear here once they join the consultation</p>
                </div>
            `;
        }
    }

    function handleUserLeft(user) {
        delete remoteUsers[user.uid];
        document.getElementById('remote-video').innerHTML = `
            <div class="video-placeholder" id="remote-placeholder">
                <i class="fas fa-user-injured"></i>
                <h3>Patient Disconnected</h3>
                <p>The patient has left the consultation</p>
            </div>
        `;
    }

    async function toggleMic() {
        if (!localTracks.audioTrack) return;
        
        if (localTracks.audioTrack.enabled) {
            await localTracks.audioTrack.setEnabled(false);
            document.getElementById('mic-toggle').classList.remove('active');
            showNotification('Microphone muted', 'info');
        } else {
            await localTracks.audioTrack.setEnabled(true);
            document.getElementById('mic-toggle').classList.add('active');
            showNotification('Microphone unmuted', 'info');
        }
    }

    async function toggleCamera() {
        if (!localTracks.videoTrack) return;
        
        if (localTracks.videoTrack.enabled) {
            await localTracks.videoTrack.setEnabled(false);
            document.getElementById('camera-toggle').classList.remove('active');
            showNotification('Camera turned off', 'info');
        } else {
            await localTracks.videoTrack.setEnabled(true);
            document.getElementById('camera-toggle').classList.add('active');
            showNotification('Camera turned on', 'info');
        }
    }

    async function toggleScreenShare() {
        if (localTracks.screenTrack) {
            await agoraClient.unpublish(localTracks.screenTrack);
            localTracks.screenTrack.close();
            localTracks.screenTrack = null;
            document.getElementById('screen-share').classList.remove('active');
            showNotification('Screen sharing stopped', 'info');
        } else {
            try {
                localTracks.screenTrack = await AgoraRTC.createScreenVideoTrack();
                await agoraClient.publish(localTracks.screenTrack);
                document.getElementById('screen-share').classList.add('active');
                showNotification('Screen sharing started', 'info');
            } catch (error) {
                showNotification('Error starting screen share: ' + error.message, 'error');
            }
        }
    }

    async function endCall() {
        // Stop all tracks
        for (let track of Object.values(localTracks)) {
            if (track) {
                track.close();
            }
        }
        
        // Leave channel
        if (agoraClient) {
            await agoraClient.leave();
        }
        
        // Reset UI
        document.getElementById('local-video').innerHTML = `
            <div class="video-placeholder" id="local-placeholder">
                <i class="fas fa-user-md"></i>
            </div>
        `;
        document.getElementById('remote-video').innerHTML = `
            <div class="video-placeholder" id="remote-placeholder">
                <i class="fas fa-user-injured"></i>
                <h3>Waiting for Patient to Join</h3>
                <p>Your patient will appear here once they join the consultation</p>
            </div>
        `;
        document.getElementById('connection-status').classList.remove('connected');
        document.getElementById('connection-text').textContent = 'Disconnected';
        
        // Stop timer
        clearInterval(callTimer);
        callDuration = 0;
        document.getElementById('timer-display').textContent = '00:00';
        
        // Reset tracks and client
        localTracks = {
            audioTrack: null,
            videoTrack: null,
            screenTrack: null
        };
        remoteUsers = {};
        agoraClient = null;
        
        showNotification('Call ended', 'info');
        
        // Update appointment status to completed
        if (currentAppointment) {
            updateAppointmentStatusToCompleted(currentAppointment.id);
        }
    }

    function startCallTimer() {
        callDuration = 0;
        clearInterval(callTimer);
        callTimer = setInterval(() => {
            callDuration++;
            const minutes = Math.floor(callDuration / 60);
            const seconds = callDuration % 60;
            document.getElementById('timer-display').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function updateAppointmentStatusToActive(appointmentId) {
        fetch('../backend/update_appointment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                doctor_id: doctorData.id,
                status: 'active',
                notes: 'Video consultation started'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const appointment = doctorData.appointments.find(appt => appt.id === appointmentId);
                if (appointment) {
                    appointment.status = 'active';
                    appointment.notes = 'Video consultation started';
                }
                loadAppointments('appointmentsContainer');
            }
        })
        .catch(error => {
            showNotification('Error updating appointment status: ' + error.message, 'error');
        });
    }

    function updateAppointmentStatusToCompleted(appointmentId) {
        fetch('../backend/update_appointment_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                doctor_id: doctorData.id,
                status: 'completed',
                notes: 'Video consultation completed'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const appointment = doctorData.appointments.find(appt => appt.id === appointmentId);
                if (appointment) {
                    appointment.status = 'completed';
                    appointment.notes = 'Video consultation completed';
                }
                loadAppointments('appointmentsContainer');
            }
        })
        .catch(error => {
            showNotification('Error updating appointment status: ' + error.message, 'error');
        });
    }

    /* ================ OTHER FUNCTIONALITIES ================ */
    function viewPrescriptionDetails(prescriptionId) {
        // Implement prescription details view
        showNotification('Viewing prescription details for ID: ' + prescriptionId, 'info');
    }

    function approvePrescription(prescriptionId) {
        fetch('../backend/approve_prescription.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                prescription_id: prescriptionId,
                doctor_id: doctorData.id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Prescription approved successfully', 'success');
                doctorData.prescriptions = doctorData.prescriptions.filter(p => p.id !== prescriptionId);
                loadPrescriptions('prescriptionsContainer');
            } else {
                showNotification('Failed to approve prescription: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error approving prescription: ' + error.message, 'error');
        });
    }

    function markLabReviewed(labId, labCard) {
        fetch('../backend/mark_lab_reviewed.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                lab_id: labId,
                doctor_id: doctorData.id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Lab result marked as reviewed', 'success');
                labCard.querySelector('.lab-status').className = 'lab-status status-completed';
                labCard.querySelector('.lab-status').textContent = 'Reviewed';
                labCard.querySelector('.mark-reviewed').remove();
                doctorData.labResults = doctorData.labResults.filter(lab => lab.id !== parseInt(labId));
            } else {
                showNotification('Failed to mark lab result as reviewed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error marking lab result as reviewed: ' + error.message, 'error');
        });
    }

    function addPatient() {
        const patientData = {
            firstName: document.getElementById('patientFirstName').value,
            lastName: document.getElementById('patientLastName').value,
            email: document.getElementById('patientEmail').value,
            phone: document.getElementById('patientPhone').value,
            dob: document.getElementById('patientDob').value
        };

        fetch('../../backend/add_patient.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(patientData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Patient added successfully', 'success');
                doctorData.patients.push({
                    id: data.patient_id,
                    first_name: patientData.firstName,
                    last_name: patientData.lastName,
                    email: patientData.email
                });
                loadPatients('patientsContainer');
                document.getElementById('patientForm').reset();
            } else {
                showNotification('Failed to add patient: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error adding patient: ' + error.message, 'error');
        });
    }

    function uploadLabResult() {
        const labData = {
            patientId: document.getElementById('labPatient').value,
            testType: document.getElementById('labTestType').value,
            testDate: document.getElementById('labTestDate').value,
            interpretation: document.getElementById('labInterpretation').value,
            doctorId: doctorData.id
        };

        const fileInput = document.getElementById('labResultFile');
        const formData = new FormData();
        formData.append('patient_id', labData.patientId);
        formData.append('test_type', labData.testType);
        formData.append('test_date', labData.testDate);
        formData.append('interpretation', labData.interpretation);
        formData.append('doctor_id', labData.doctorId);
        if (fileInput.files[0]) {
            formData.append('result_file', fileInput.files[0]);
        }

        fetch('../backend/upload_lab_result.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Lab result uploaded successfully', 'success');
                doctorData.labResults.push({
                    id: data.lab_id,
                    patient_id: labData.patientId,
                    patient_first_name: doctorData.patients.find(p => p.id == labData.patientId)?.first_name || '',
                    patient_last_name: doctorData.patients.find(p => p.id == labData.patientId)?.last_name || '',
                    test_type: labData.testType,
                    test_date: labData.testDate,
                    result_details: labData.interpretation,
                    status: 'pending'
                });
                loadLabResults('labResultsContainer');
                document.getElementById('labUploadForm').reset();
            } else {
                showNotification('Failed to upload lab result: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error uploading lab result: ' + error.message, 'error');
        });
    }

    function viewPatientDetails(patientId) {
        showNotification('Viewing patient details for ID: ' + patientId, 'info');
    }

    function scheduleAppointment(patientId) {
        showNotification('Scheduling appointment for patient ID: ' + patientId, 'info');
    }

    /* ================ NOTIFICATION SYSTEM ================ */
    function showNotification(message, type) {
        const notificationContainer = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `app-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">${message}</div>
            <button class="close-btn">×</button>
        `;
        
        notificationContainer.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
        
        notification.querySelector('.close-btn').addEventListener('click', () => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
    }

    /* ================ PAGE INITIALIZATION ================ */
    window.initDoctorPage = function() {
        // Initialize dashboard components
        if (document.getElementById('appointmentsContainer')) {
            loadAppointments('appointmentsContainer');
        }
        if (document.getElementById('prescriptionsContainer')) {
            loadPrescriptions('prescriptionsContainer');
        }
        if (document.getElementById('labResultsContainer')) {
            loadLabResults('labResultsContainer');
        }
        if (document.getElementById('patientsContainer')) {
            loadPatients('patientsContainer');
        }
        if (document.getElementById('labPatient')) {
            const select = document.getElementById('labPatient');
            select.innerHTML = '<option value="">Select patient</option>';
            doctorData.patients.forEach(patient => {
                select.innerHTML += `<option value="${patient.id}">${patient.first_name} ${patient.last_name}</option>`;
            });
        }
    };

    // Handle browser back/forward navigation
    window.addEventListener('popstate', (event) => {
        if (event.state && event.state.page) {
            loadMainContent(event.state.page);
        }
    });
    </script>
</body>
</html>