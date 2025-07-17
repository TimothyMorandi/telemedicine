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

// Get user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

// Fetch doctors from the database
$doctors = [];
$query = "SELECT id, first_name, last_name, specialty, hospital_name FROM users WHERE user_type = 'doctor'";
$result = mysqli_query($con, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
}

// Get the requested page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Fetch upcoming appointments for the current user
$appointments = [];
$appointmentQuery = "SELECT a.*, 
                    d.first_name AS doctor_first_name, 
                    d.last_name AS doctor_last_name,
                    d.specialty AS doctor_specialty
                    FROM appointments a
                    JOIN users d ON a.doctor_id = d.id
                    WHERE a.patient_id = $user_id
                    AND a.status = 'confirmed'
                    AND a.appointment_date >= CURDATE()
                    ORDER BY a.appointment_date ASC";
$appointmentResult = mysqli_query($con, $appointmentQuery);
if ($appointmentResult) {
    while ($row = mysqli_fetch_assoc($appointmentResult)) {
        $appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.23.1.js"></script>

    <style>
        /* All CSS styles remain unchanged */
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        /* ... (All other CSS styles remain exactly the same) ... */
    
        body {
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
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
            background: var(--primary);
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
            color: var(--primary);
        }
        
        .nav-item a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .nav-item.active a {
            background: rgba(42, 134, 255, 0.15);
            color: var(--primary);
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
        }
        
        .btn-signout i {
            margin-right: 8px;
        }
        
        .btn-signout:hover {
            background: rgba(234, 67, 53, 0.08);
            color: var(--danger);
            border-color: rgba(234, 67, 53, 0.3);
        }
        
        .sidebar-footer {
            padding: 15px;
            font-size: 0.75rem;
            color: var(--text-light);
            text-align: center;
            border-top: 1px solid var(--border);
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
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
            color: var(--primary);
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
            background: var(--primary);
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
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
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
            background: var(--primary);
            border-radius: 3px;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .icon-blue {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .icon-green {
            background: var(--secondary-light);
            color: var(--secondary);
        }
        
        .icon-orange {
            background: var(--accent-light);
            color: var(--accent);
        }
        
        .icon-red {
            background: var(--danger-light);
            color: var(--danger);
        }
        
        .card-content {
            margin-top: 15px;
        }
        
        /* Form Styles */
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
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(42, 134, 255, 0.2);
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f1f3f4;
            color: var(--text-dark);
        }
        
        .btn-secondary:hover {
            background: #e8eaed;
        }
        
        .btn-danger {
            background: var(--danger-light);
            color: var(--danger);
        }
        
        .btn-danger:hover {
            background: #fad7d4;
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        
        .data-table th {
            background: var(--primary-light);
            color: var(--primary-dark);
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .data-table tr:hover {
            background: rgba(42, 134, 255, 0.03);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-confirmed {
            background: rgba(52, 168, 83, 0.1);
            color: var(--secondary);
        }
        
        .status-pending {
            background: rgba(251, 188, 5, 0.1);
            color: var(--accent);
        }
        
        .status-canceled {
            background: rgba(234, 67, 53, 0.1);
            color: var(--danger);
        }
        
        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-medium);
        }
        
        .modal-body {
            margin-bottom: 25px;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
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
            
            .submenu {
                padding-left: 0;
            }
            
            .submenu .nav-item a {
                padding: 12px 5px;
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
            
            .sidebar-nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px;
            }
            
            .nav-section {
                display: none;
            }
            
            .nav-item {
                margin: 5px;
                flex: 0 0 calc(20% - 10px);
            }
            
            .nav-divider, .sidebar-signout {
                display: none;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .date-display {
                width: 100%;
            }
        }
        
        /* Doctor Card Styles */
        .doctor-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: var(--transition);
            background: white;
        }
        
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .doctor-avatar {
            height: 150px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .doctor-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .doctor-avatar i {
            font-size: 4rem;
            color: var(--primary);
        }
        
        .doctor-info {
            padding: 20px;
        }
        
        .doctor-name {
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        
        .doctor-specialty {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .doctor-experience {
            color: var(--text-medium);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .doctor-actions {
            display: flex;
            gap: 10px;
        }
        
        .doctor-actions .btn {
            flex: 1;
            padding: 10px;
            font-size: 0.9rem;
        }
        
        /* Appointment form styles */
        .appointment-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .appointment-form .form-group.full-width {
            grid-column: span 2;
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
            color: var(--primary);
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: var(--text-medium);
        }
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
        
        /* Animation for new notifications */
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        .app-notification {
            animation: slideIn 0.3s ease forwards;
        }
        
        /* Labs specific styles */
        .labs-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .lab-feature-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 25px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .lab-feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }
        
        .lab-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .lab-icon.upload {
            background: rgba(66, 133, 244, 0.15);
            color: #4285F4;
        }
        
        .lab-icon.referral {
            background: rgba(52, 168, 83, 0.15);
            color: var(--secondary);
        }
        
        .lab-icon.symptom {
            background: rgba(251, 188, 5, 0.15);
            color: var(--accent);
        }
        
        .lab-icon.chat {
            background: rgba(234, 67, 53, 0.15);
            color: var(--danger);
        }
        
        .lab-icon.kit {
            background: rgba(171, 71, 188, 0.15);
            color: #AB47BC;
        }
        
        .lab-icon.report {
            background: rgba(0, 150, 136, 0.15);
            color: #009688;
        }
        
        .lab-icon.history {
            background: rgba(57, 73, 171, 0.15);
            color: #3949ab;
        }
        
        .lab-icon.viewer {
            background: rgba(0, 131, 143, 0.15);
            color: #00838f;
        }
        
        .lab-icon.upcoming {
            background: rgba(123, 31, 162, 0.15);
            color: #7b1fa2;
        }
        
        .lab-icon.request {
            background: rgba(230, 81, 0, 0.15);
            color: #e65100;
        }
        
        .lab-icon.notification {
            background: rgba(191, 54, 12, 0.15);
            color: #bf360c;
        }
        
        .lab-card-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-dark);
        }
        
        .lab-card-content {
            flex: 1;
            margin-bottom: 20px;
            color: var(--text-medium);
        }
        
        .lab-test-history, .lab-orders-list {
            margin-top: 15px;
        }
        
        .lab-history-item, .lab-order-item {
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        
        .lab-history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .lab-history-name {
            font-weight: 500;
            flex: 2;
        }
        
        .lab-history-date {
            color: var(--text-medium);
            flex: 1;
            text-align: right;
            margin-right: 15px;
        }
        
        .lab-order-item {
            background: white;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .order-details {
            color: var(--text-medium);
            margin-bottom: 10px;
        }
        
        .order-details i {
            width: 20px;
            color: var(--primary);
        }
        
        .order-instructions {
            background: var(--primary-light);
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 0.9rem;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .result-viewer {
            margin-top: 20px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 15px;
        }
        
        .result-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .result-values {
            margin-bottom: 20px;
        }
        
        .result-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .result-item.warning {
            background: rgba(251, 188, 5, 0.08);
        }
        
        .result-name {
            flex: 2;
            font-weight: 500;
        }
        
        .result-value {
            flex: 1;
            font-weight: 600;
        }
        
        .result-range {
            flex: 2;
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        
        .result-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-left: 10px;
        }
        
        .result-indicator.normal {
            background: var(--success);
        }
        
        .result-indicator.warning {
            background: var(--accent);
        }
        
        .result-indicator.danger {
            background: var(--danger);
        }
        
        .doctor-remarks {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .notification-settings {
            margin-top: 15px;
        }
        
        .setting-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-right: 15px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--primary);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .result-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
            display: inline-block;
        }
        
        .result-status.normal {
            background: rgba(52, 168, 83, 0.1);
            color: var(--success);
        }
        
        .result-status.warning {
            background: rgba(251, 188, 5, 0.1);
            color: var(--accent);
        }
        
        .result-status.danger {
            background: rgba(234, 67, 53, 0.1);
            color: var(--danger);
        }
        
        .section-title-sm {
            font-size: 1.2rem;
            margin: 25px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .result-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .lab-upload-area {
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .lab-upload-area:hover {
            border-color: var(--primary);
            background: rgba(42, 134, 255, 0.05);
        }
        
        .lab-upload-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .lab-status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: rgba(251, 188, 5, 0.1);
            color: var(--accent);
        }
        
        .status-completed {
            background: rgba(52, 168, 83, 0.1);
            color: var(--secondary);
        }
        
        .status-scheduled {
            background: rgba(57, 73, 171, 0.1);
            color: #3949ab;
        }
        /* Adding specific styles for the video section */
        .video-player {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remote-video-container, 
.local-video-container {
  position: relative;
  overflow: hidden;
}

.local-video-container {
  position: absolute;
  bottom: 20px;
  right: 20px;
  width: 180px;
  height: 135px;
  border-radius: 8px;
  border: 2px solid #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  z-index: 10;
}
        /* Adding specific styles for the video section */
        
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
            color: #2a86ff;
            font-size: 1.1rem;
        }
        
        .appointment-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-upcoming {
            background: rgba(251, 188, 5, 0.1);
            color: #fbbc05;
        }
        
        .status-active {
            background: rgba(52, 168, 83, 0.1);
            color: #34a853;
        }
        
        .status-completed {
            background: rgba(66, 133, 244, 0.1);
            color: #4285f4;
        }
        
        .appointment-doctor {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .doctor-avatar-small {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e3f0ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2a86ff;
            font-weight: bold;
        }
        
        .doctor-info-small h4 {
            margin-bottom: 5px;
        }
        
        .doctor-info-small p {
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
        }
        
        .no-appointments {
            text-align: center;
            padding: 40px;
            color: #5f6368;
            grid-column: 1 / -1;
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .video-container {
                height: auto;
                min-height: 70vh;
            }
            
            .video-content {
                flex-direction: column;
            }
            
            .local-video-container {
                position: relative;
                width: 100%;
                height: 200px;
                margin-top: 15px;
                bottom: auto;
                right: auto;
            }
            
            .video-controls {
                padding: 10px;
                gap: 10px;
            }
            
            .control-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .appointments-container {
                grid-template-columns: 1fr;
            }
        
        }
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
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCAxNDAgNDAiPjxwYXRoIGQ9Ik0xMCAxMEg0MFYzMEgxMFoiIGZpbGw9IiMyQTg2RkYiLz48cGF0aCBkPSJNNDAgMTBINzBWMzBINDBaIiBmaWxsPSIjMUEzQTVGIi8+PHBhdGggZD0iTTcwIDEwSDEwMFYzMEg3MFoiIGZpbGw9IiMyQTg2RkYiLz48cGF0aCBkPSJNMTQwIDEwSDExMFYzMEgxNDBaIiBmaWxsPSIjMUEzQTVGIi8+PHRleHQgeD0iNDAiIHk9IjM1IiBmb250LWZhbWlseT0iUG9wcGlucyIgZm9udC1zaXplPSIxNiIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0iIzFBM0E1RiI+TElWQTwvdGV4dD48L3N2Zz4=" alt="Telemedicine Logo">
        </div>
        
        <div class="user-profile">
            <div class="user-avatar"><?= substr($_SESSION['first_name'], 0, 1) ?></div>
            <div class="user-info">
                <h3><?= $_SESSION['first_name'] ?></h3>
                <p>Patient</p>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section-title">Main Navigation</div>
            <ul>
                <li class="nav-item"><a href="dashboard.php" data-content="dashboard.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
                <li class="nav-item"><a href="index.php" data-content="index.php"><i class="far fa-calendar-alt"></i> <span>Appointment</span></a></li>
                <li class="nav-item"><a href="#" data-content="schedule.php"><i class="far fa-calendar-check"></i> <span>Schedule</span></a></li>
                <li class="nav-item"><a href="#" data-content="doctor.php"><i class="fas fa-user-md"></i> <span>Doctor</span></a></li>
            </ul>
            
            <div class="nav-section-title">Health Services</div>
            <ul>
                <li class="nav-item"><a href="#" data-content="labs.php"><i class="fas fa-flask"></i> <span>Labs</span></a></li>
                <li class="nav-item"><a href="#" data-content="medicine.php"><i class="fas fa-pills"></i> <span>Medicine</span></a></li>
                <li class="nav-item"><a href="#" data-content="medicaltest.php"><i class="fas fa-microscope"></i> <span>Medical Test</span></a></li>
                <li class="nav-item"><a href="#" data-content="prescription.php"><i class="fas fa-file-prescription"></i> <span>Prescription</span></a></li>
                <li class="nav-item"><a href="healthrec.php" data-content="healthrec.php"><i class="fas fa-notes-medical"></i> <span>Health Record</span></a></li>
                <li class="nav-item"><a href="#" data-content="video.php"><i class="fas fa-video"></i> <span>Video Call</span></a></li>
            </ul>
            
            <div class="nav-section-title">Bookings</div>
            <ul>
                <li class="nav-item"><a href="online_preadmission.php" data-content="online_preadmission.php"><i class="far fa-edit"></i> <span>Online Pre-Admission</span></a></li>
                <li class="nav-item"><a href="ambulance.php" data-content="ambulance.php"><i class="fas fa-ambulance"></i> <span>Ambulance Booking</span></a></li>
            </ul>
            
            <div class="nav-section-title">Special Groups</div>
            <ul>
                <li class="nav-item has-submenu">
                    <a href="#"><i class="fas fa-notes-medical"></i> <span>Special Attentions</span></a>
                    <ul class="submenu">
                        <li class="nav-item"><a href="pregnat.php" data-content="pregnat.php"><i class="fas fa-pregnant"></i> <span>Pregnant</span></a></li>
                        <li class="nav-item"><a href="baby.php" data-content="baby.php"><i class="fas fa-baby"></i> <span>Baby</span></a></li>
                        <li class="nav-item"><a href="hiv.php" data-content="hiv.php"><i class="fas fa-notes-medical"></i> <span>HIV/AIDS</span></a></li>
                    </ul>
                </li>
            </ul>
            
            <div class="nav-divider"></div>
            
            <div class="nav-section-title">Preferences</div>
            <ul>
                <li class="nav-item"><a href="#" data-content="general.php"><i class="fas fa-cog"></i> <span>General</span></a></li>
                <li class="nav-item"><a href="#" data-content="language.php"><i class="fas fa-language"></i> <span>Language</span></a></li>
                <li class="nav-item"><a href="#" data-content="support.php"><i class="fas fa-question-circle"></i> <span>Support</span></a></li>
            </ul>
        </nav>
        
        <div class="sidebar-signout">
            <button class="btn-signout" id="signOutBtn"><i class="fas fa-sign-out-alt"></i> Sign Out</button>
        </div>
        
        <div class="sidebar-footer">
            <p>Copyright &copy; <?= date('Y') ?> Telemedicine Health. All rights reserved.</p>
        </div>
        
        <!-- Sign Out Modal -->
        <div class="modal" id="confirm-sign-out" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Confirm Sign Out</h3>
                    <button class="close-modal">&times;</button>
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
    </aside>

    <div class="main-content" id="main-content">
        <!-- Content will be loaded dynamically here -->
    </div>
    
    <!-- Notification Container -->
    <div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>

   <script>
    /* ================ GLOBAL SCRIPTS (All Pages) ================ */
// Pass PHP doctors data to JavaScript
const doctorsData = <?php echo json_encode($doctors); ?>;
// Global variables
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

document.addEventListener('DOMContentLoaded', function() {
    // Initialize page
    if (window.initDashboardPage) {
        window.initDashboardPage();
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
            const url = this.getAttribute('data-content');
            const page = url.split('.')[0];
            
            // Update active state
            document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
                item.classList.remove('active');
            });
            parentItem.classList.add('active');
            
            // Load content
            loadMainContent(url, page);
        });
    });
    
    // Sign out functionality
    const signOutBtn = document.getElementById('signOutBtn');
    const confirmModal = document.getElementById('confirm-sign-out');
    
    if (signOutBtn) {
        signOutBtn.addEventListener('click', function() {
            confirmModal.style.display = 'flex';
        });
    }
    
    document.getElementById('confirm-sign-out-yes')?.addEventListener('click', function() {
        window.location.href = '../../login.php';
    });
    
    document.getElementById('confirm-sign-out-no')?.addEventListener('click', function() {
        confirmModal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === confirmModal) {
            confirmModal.style.display = 'none';
        }
    });
    
    // Initialize Home as active
    const dashboardLink = document.querySelector('.nav-item a[data-content="dashboard.php"]');
    if (dashboardLink) {
        dashboardLink.parentNode.classList.add('active');
    }
    
    // Load initial page content
    const initialPage = "<?= $page ?>";
    const initialUrl = initialPage === 'dashboard' ? 'dashboard.php' : `${initialPage}.php`;
    loadMainContent(initialUrl, initialPage, false);
});

function loadMainContent(url, page, pushState = true) {
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
        
        if (pushState) {
            // Update URL without reloading page
            window.history.pushState({ page }, '', `?page=${page}`);
        }
        
        // Re-init page scripts
        if (window.initDashboardPage) {
            window.initDashboardPage();
        }
        
        // Initialize specific page components
        if (page === 'index') {
            loadAppointments('appointmentsTableBody', 'table');
        }
        else if (page === 'dashboard') {
            loadAppointmentCount();
        }
        else if (page === 'labs') {
            initLabsPage();
        }
        else if (page === 'video') {
            initVideoPage();
        }
        else if (page === 'doctor') {
            loadAppointments('appointmentsTableBody', 'table');
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
                            <h1>Welcome Back, <?= $_SESSION['first_name'] ?></h1>
                            <p>Here's what's happening with your health today</p>
                        </div>
                        <div class="date-display">
                            <i class="fas fa-calendar"></i>
                            <span>${today}</span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Health Overview</div>
                        </div>
                        <div class="card-content">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                            <!-- Upcoming Appointments Card -->
                            <div class="card">
        <div class="card-header">
            <div class="card-title">Upcoming Appointments</div>
            <div class="card-icon icon-blue">
                <i class="far fa-calendar-alt"></i>
            </div>
        </div>
        <div class="card-content">
            <!-- Add ID for dynamic update -->
            <div class="stat-number" id="appointment-count">0</div>
            <div class="stat-label">Scheduled appointments</div>
        </div>
    </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Recent Test Results</div>
                                        <div class="card-icon icon-green">
                                            <i class="fas fa-file-medical"></i>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="stat-number">5</div>
                                        <div class="stat-label">Tests completed</div>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Medication</div>
                                        <div class="card-icon icon-orange">
                                            <i class="fas fa-pills"></i>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="stat-number">3</div>
                                        <div class="stat-label">Active prescriptions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="section-title">Quick Actions</div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; margin-bottom: 40px;">
                        <a href="#" class="card" onclick="loadMainContent('index.php', 'index')">
                            <div style="text-align: center; padding: 20px;">
                                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 15px;">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                                <h3>Book Appointment</h3>
                            </div>
                        </a>
                        
                        <a href="#" class="card" onclick="loadMainContent('prescription.php', 'prescription')">
                            <div style="text-align: center; padding: 20px;">
                                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 15px;">
                                    <i class="fas fa-file-prescription"></i>
                                </div>
                                <h3>Prescriptions</h3>
                            </div>
                        </a>
                        
                        <a href="#" class="card" onclick="loadMainContent('healthrec.php', 'healthrec')">
                            <div style="text-align: center; padding: 20px;">
                                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 15px;">
                                    <i class="fas fa-notes-medical"></i>
                                </div>
                                <h3>Health Records</h3>
                            </div>
                        </a>
                        
                        <a href="#" class="card" onclick="loadMainContent('video.php', 'video')">
                            <div style="text-align: center; padding: 20px;">
                                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 15px;">
                                    <i class="fas fa-video"></i>
                                </div>
                                <h3>Video Call</h3>
                            </div>
                        </a>
                    </div>
                    
                    <div class="section-title">Recent Activities</div>
                    
                    <div class="card">
                        <div class="card-content">
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div style="display: flex; align-items: center; gap: 15px; padding: 10px;">
                                    <div style="background: var(--primary-light); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-prescription-bottle-alt" style="color: var(--primary);"></i>
                                    </div>
                                    <div>
                                        <h3 style="margin-bottom: 5px;">Prescription Refilled</h3>
                                        <p style="color: var(--text-medium);">Your prescription for Metformin has been refilled</p>
                                        <p style="color: var(--text-light); font-size: 0.9rem;">Yesterday at 2:30 PM</p>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 15px; padding: 10px;">
                                    <div style="background: var(--secondary-light); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-file-medical" style="color: var(--secondary);"></i>
                                    </div>
                                    <div>
                                        <h3 style="margin-bottom: 5px;">Test Results Available</h3>
                                        <p style="color: var(--text-medium);">Your blood test results are now available</p>
                                        <p style="color: var(--text-light); font-size: 0.9rem;">2 days ago</p>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 15px; padding: 10px;">
                                    <div style="background: var(--accent-light); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-calendar-check" style="color: var(--accent);"></i>
                                    </div>
                                    <div>
                                        <h3 style="margin-bottom: 5px;">Appointment Confirmed</h3>
                                        <p style="color: var(--text-medium);">Your appointment with Dr. Smith has been confirmed</p>
                                        <p style="color: var(--text-light); font-size: 0.9rem;">3 days ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'index': // Appointment
            // Generate doctor options for dropdown
            let doctorOptions = '';
            if (doctorsData && doctorsData.length > 0) {
                doctorsData.forEach(doctor => {
                    doctorOptions += `<option value="${doctor.id}">Dr. ${doctor.first_name} ${doctor.last_name} (${doctor.specialty})</option>`;
                });
            } else {
                doctorOptions = '<option>No doctors available</option>';
            }
            
            content = `
                <div class="dashboard-container">
                    <div class="dashboard-header">
                        <div class="welcome-message">
                            <h1>Appointments</h1>
                            <p>Schedule and manage your appointments</p>
                        </div>
                        <div class="date-display">
                            <i class="fas fa-calendar"></i>
                            <span>${today}</span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Schedule New Appointment</div>
                        </div>
                        <div class="card-content">
                            <form id="appointmentForm" class="appointment-form">
                                <div class="form-group">
                                    <label class="form-label">Select Speciality</label>
                                    <select class="form-control" id="specialitySelect">
                                        <option value="Cardiology">Cardiology</option>
                                        <option value="Dermatology">Dermatology</option>
                                        <option value="Neurology">Neurology</option>
                                        <option value="Orthopedics">Orthopedics</option>
                                        <option value="Pediatrics">Pediatrics</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Select Doctor</label>
                                    <select class="form-control" id="doctorSelect">
                                        ${doctorOptions}
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Appointment Date</label>
                                    <input type="date" class="form-control" id="appointmentDate">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Preferred Time</label>
                                    <select class="form-control" id="appointmentTime">
                                        <option>Morning (9 AM - 12 PM)</option>
                                        <option>Afternoon (1 PM - 4 PM)</option>
                                        <option>Evening (5 PM - 7 PM)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label class="form-label">Reason for Visit</label>
                                    <textarea class="form-control" rows="3" id="appointmentReason" placeholder="Briefly describe the reason for your appointment"></textarea>
                                </div>
                                
                                <div class="form-group full-width" style="display: flex; justify-content: flex-end;">
                                    <button type="button" class="btn btn-primary" onclick="scheduleAppointment()">Schedule Appointment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="section-title">Upcoming Appointments</div>
                    
                    <div class="card">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Speciality</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="appointmentsTableBody">
                                    <tr>
                                        <td colspan="5" class="loading">
                                            <i class="fas fa-spinner fa-spin"></i> Loading appointments...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            break;
            
        case 'doctor':
            // Generate doctor cards
            let doctorCards = '';
            if (doctorsData && doctorsData.length > 0) {
                doctorsData.forEach(doctor => {
                    doctorCards += `
                        <div class="doctor-card">
                            <div class="doctor-avatar">
                                ${doctor.profile_img ? 
                                    `<img src="${doctor.profile_img}" alt="Dr. ${doctor.first_name} ${doctor.last_name}">` : 
                                    `<i class="fas fa-user-md"></i>`
                                }
                            </div>
                            <div class="doctor-info">
                                <h3 class="doctor-name">Dr. ${doctor.first_name} ${doctor.last_name}</h3>
                                <p class="doctor-specialty">${doctor.specialty}</p>
                                <p class="doctor-experience">${doctor.experience || '5+'} years of experience</p>
                                <div class="doctor-actions">
                                    <button class="btn btn-primary" data-doctor-id="${doctor.id}">View Profile</button>
                                    <button class="btn btn-secondary" data-doctor-id="${doctor.id}">Book Appointment</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                doctorCards = '<div class="no-data"><p>No doctors found in the system</p></div>';
            }
            
            content = `
                <div class="dashboard-container">
                    <div class="dashboard-header">
                        <div class="welcome-message">
                            <h1>Doctors</h1>
                            <p>Find and connect with healthcare providers</p>
                        </div>
                        <div class="date-display">
                            <i class="fas fa-calendar"></i>
                            <span>${today}</span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Find a Doctor</div>
                        </div>
                        <div class="card-content">
                            <div style="display: grid; grid-template-columns: 1fr auto; gap: 15px; margin-bottom: 20px;">
                                <input type="text" class="form-control" placeholder="Search by name, specialty, or location">
                                <button class="btn btn-primary">Search</button>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                                ${doctorCards}
                            </div>
                        </div>
                    </div>
                    
                    <div class="section-title">My Doctors</div>
                    
                    <div class="card">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Doctor</th>
                                        <th>Speciality</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="appointmentsTableBody">
                                    <tr>
                                        <td colspan="5" class="loading">
                                            <i class="fas fa-spinner fa-spin"></i> Loading appointments...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            break;
            
            case 'labs':
        // We'll generate this content dynamically after fetching data
        content = `
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <div class="welcome-message">
                        <h1>Lab Services</h1>
                        <p>Manage your lab tests and results</p>
                    </div>
                    <div class="date-display">
                        <i class="fas fa-flask"></i>
                        <span id="current-date">${today}</span>
                    </div>
                </div>
                
                <div class="labs-container">
                    <!-- Lab Test History -->
                    <div class="lab-feature-card">
                        <div class="lab-icon history">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3 class="lab-card-title">Lab Test History</h3>
                        <p class="lab-card-content">View your complete lab test history</p>
                        
                        <div class="lab-test-history">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i> Loading history...
                            </div>
                        </div>
                        
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="showAllTests()">
                            View Full History
                        </button>
                    </div>
                    
                    <!-- Test Results Viewer -->
                    <div class="lab-feature-card">
                        <div class="lab-icon viewer">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <h3 class="lab-card-title">Test Results Viewer</h3>
                        <p class="lab-card-content">View detailed lab reports and interpretations</p>
                        
                        <div class="form-group">
                            <label class="form-label">Select Test Report</label>
                            <select class="form-control" id="test-report-select">
                                <option value="">Loading reports...</option>
                            </select>
                        </div>
                        
                        <div id="result-viewer" style="display: none;">
                            <!-- Will be populated when a report is selected -->
                        </div>
                    </div>
                    
                    <!-- Upload External Results -->
                    <div class="lab-feature-card">
                        <div class="lab-icon upload">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <h3 class="lab-card-title">Upload External Results</h3>
                        <p class="lab-card-content">Upload lab reports from other facilities</p>
                        
                        <div class="lab-upload-area" id="upload-area">
                            <div class="lab-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p>Click to upload or drag & drop files</p>
                            <p class="text-small">(PDF, JPG, PNG - max 10MB)</p>
                        </div>
                        <input type="file" id="file-input" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                        
                        <div class="form-group">
                            <label class="form-label">Test Type</label>
                            <input type="text" class="form-control" id="external-test-type" placeholder="Enter test name">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Test Date</label>
                            <input type="date" class="form-control" id="external-test-date">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Facility Name</label>
                            <input type="text" class="form-control" id="external-facility" placeholder="Lab facility name">
                        </div>
                        
                        <button class="btn btn-primary" id="upload-external-btn">Upload Results</button>
                    </div>
                    
                    <!-- Upcoming Lab Orders -->
                    <div class="lab-feature-card">
                        <div class="lab-icon upcoming">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="lab-card-title">Upcoming Lab Orders</h3>
                        <p class="lab-card-content">Your scheduled and pending lab tests</p>
                        
                        <div class="lab-orders-list">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i> Loading orders...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Request New Lab Test -->
                    <div class="lab-feature-card">
                        <div class="lab-icon request">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3 class="lab-card-title">Request New Lab Test</h3>
                        <p class="lab-card-content">Request a new lab test from your doctor</p>
                        
                        <div class="form-group">
                            <label class="form-label">Select Test Type</label>
                            <select class="form-control" id="test-type-request">
                                <option value="">Select test type</option>
                                <option value="blood">Blood Test</option>
                                <option value="urine">Urine Analysis</option>
                                <option value="imaging">Imaging (X-ray, MRI)</option>
                                <option value="biopsy">Biopsy</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Specific Test Name</label>
                            <input type="text" class="form-control" id="test-name-request" placeholder="Enter test name">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Reason for Request</label>
                            <textarea class="form-control" rows="3" id="test-reason" placeholder="Explain why you need this test"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Preferred Doctor</label>
                            <select class="form-control" id="doctor-request">
                                <option value="">Loading doctors...</option>
                            </select>
                        </div>
                        
                        <button class="btn btn-primary" id="request-test-btn">Submit Request</button>
                    </div>
                    
                    <!-- Notifications Settings -->
                    <div class="lab-feature-card">
                        <div class="lab-icon notification">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="lab-card-title">Notification Preferences</h3>
                        <p class="lab-card-content">Configure how you receive lab result notifications</p>
                        
                        <div class="notification-settings">
                            <div class="setting-item">
                                <label class="switch">
                                    <input type="checkbox" id="email-notification">
                                    <span class="slider"></span>
                                </label>
                                <span>Email Notifications</span>
                            </div>
                            
                            <div class="setting-item">
                                <label class="switch">
                                    <input type="checkbox" id="sms-notification">
                                    <span class="slider"></span>
                                </label>
                                <span>SMS Notifications</span>
                            </div>
                            
                            <div class="setting-item">
                                <label class="switch">
                                    <input type="checkbox" id="app-notification">
                                    <span class="slider"></span>
                                </label>
                                <span>In-App Notifications</span>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <label class="form-label">Notification Frequency</label>
                            <select class="form-control" id="notification-frequency">
                                <option value="immediate">Immediately when available</option>
                                <option value="daily">Daily Summary</option>
                                <option value="weekly">Weekly Summary</option>
                            </select>
                        </div>
                        
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="saveNotificationSettings()">
                            Save Preferences
                        </button>
                    </div>
                </div>
                
                <!-- Recent Lab Results Section -->
                <div class="section-title">Your Lab Results</div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Recent Test Reports</div>
                        <div class="lab-status-badge status-completed">Latest Results</div>
                    </div>
                    
                    <div class="card-content">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Test Name</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Doctor Review</th>
                                        <th>Results Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="loading">
                                            <i class="fas fa-spinner fa-spin"></i> Loading results...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Test Result Modal -->
            <div class="modal" id="test-result-modal" style="display:none;">
                <div class="modal-content" style="max-width: 800px;">
                    <div class="modal-header">
                        <h3 class="modal-title" id="modal-test-name">Loading...</h3>
                        <button class="close-modal" onclick="closeResultModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="result-meta">
                            <div><strong>Date:</strong> <span id="modal-test-date">Loading...</span></div>
                            <div><strong>Ordered by:</strong> <span id="modal-test-doctor">Loading...</span></div>
                            <div><strong>Facility:</strong> <span id="modal-test-facility">Loading...</span></div>
                        </div>
                        
                        <div class="section-title-sm">Test Results</div>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Your Value</th>
                                        <th>Standard Range</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-result-values">
                                    <tr>
                                        <td colspan="4" class="loading">
                                            <i class="fas fa-spinner fa-spin"></i> Loading results...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="section-title-sm">Doctor's Interpretation</div>
                        <div class="doctor-remarks" id="modal-doctor-remarks">
                            Loading interpretation...
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                        <button class="btn btn-danger" onclick="initiateDoctorChat()">
                            <i class="fas fa-comment-medical"></i> Talk to Doctor
                        </button>
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
                                <p>Connect with your doctor for a virtual visit</p>
                            </div>
                            <div class="date-display">
                                <i class="fas fa-video"></i>
                                <span>${today}</span>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Live Video Conference</div>
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
                                                <i class="fas fa-user-md"></i>
                                                <h3>Waiting for Doctor to Join</h3>
                                                <p>Your doctor will appear here once they join the consultation</p>
                                            </div>
                                        </div>
                                        
                                        <div class="local-video-container" id="local-video">
                                            <div class="video-placeholder" id="local-placeholder">
                                                <i class="fas fa-user"></i>
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
                                        <button class="control-btn end-call" id="end-call">
                                            <i class="fas fa-phone-slash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-title">Your Appointments</div>
                        
                        <div class="appointments-container" id="appointments-container">
                            <div class="loading" style="grid-column: 1 / -1;">
                                <i class="fas fa-spinner fa-spin"></i> Loading appointments...
                            </div>
                        </div>
                    </div>
                `;
                break;
                
            // For this example, I'll return to dashboard if page not found
            default:
                content = getPageContent('dashboard');
    }
    
    return content;
}

/* ================ DASHBOARD PAGE ================ */
function loadAppointmentCount() {
    fetch('../../backend/get_appointment_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const countElement = document.getElementById('appointment-count');
                if (countElement) {
                    countElement.textContent = data.count;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching appointment count:', error);
        });
}

/* ================ APPOINTMENTS PAGE ================ */
function scheduleAppointment() {
    const doctorSelect = document.getElementById('doctorSelect');
    const specialitySelect = document.getElementById('specialitySelect');
    const appointmentDate = document.getElementById('appointmentDate');
    const appointmentTime = document.getElementById('appointmentTime');
    const appointmentReason = document.getElementById('appointmentReason');
    const submitBtn = document.querySelector('#appointmentForm button[type="button"]');
    
    if (!doctorSelect.value || !specialitySelect.value || !appointmentDate.value || !appointmentTime.value) {
        showNotification('Please fill all required fields', 'error');
        return;
    }
    
    // Validate date
    const selectedDate = new Date(appointmentDate.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        showNotification('Appointment date cannot be in the past', 'error');
        return;
    }
    
    // Prepare form data
    const formData = {
        speciality: specialitySelect.value,
        doctor: doctorSelect.value,
        appointmentDate: appointmentDate.value,
        appointmentTime: appointmentTime.value,
        appointmentReason: appointmentReason.value
    };
    
    // Show loading state
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scheduling...';
    submitBtn.disabled = true;
    
    // Send to backend
    fetch('../../backend/save_appointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Get doctor name for display
            const doctorName = doctorSelect.options[doctorSelect.selectedIndex].text;
            
            // Show detailed success message
            const successMessage = `
                Appointment scheduled successfully!
                <br><br>
                <strong>Appointment ID:</strong> ${data.data.appointment_id}<br>
                <strong>Doctor:</strong> ${doctorName}<br>
                <strong>Speciality:</strong> ${formData.speciality}<br>
                <strong>Date:</strong> ${formatDate(formData.appointmentDate)}<br>
                <strong>Time:</strong> ${formData.appointmentTime.split(' ')[0]}<br>
                <strong>Reason:</strong> ${formData.appointmentReason || 'Not specified'}
            `;
            
            showNotification(successMessage, 'success', 5000);
            
            // Reset form
            document.getElementById('appointmentForm').reset();
            
            // Refresh appointments list
            loadAppointments('appointmentsTableBody', 'table');
            loadAppointmentCount();
        } else {
            showNotification(`Error: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
}

/* ================ STANDARDIZED APPOINTMENTS LOADER ================ */
function loadAppointments(containerId, renderType = 'table') {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    // Show loading state
    if (renderType === 'table') {
        container.innerHTML = '<tr><td colspan="5" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading appointments...</td></tr>';
    } else if (renderType === 'cards') {
        container.innerHTML = '<div class="loading" style="grid-column: 1 / -1;"><i class="fas fa-spinner fa-spin"></i> Loading appointments...</div>';
    }

    fetch('../../backend/get_appointments.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.appointments.length > 0) {
                if (renderType === 'table') {
                    renderAppointmentsTable(data.appointments, container);
                } else if (renderType === 'cards') {
                    renderAppointmentsCards(data.appointments, container);
                }
            } else {
                if (renderType === 'table') {
                    container.innerHTML = '<tr><td colspan="5" class="no-data">No upcoming appointments</td></tr>';
                } else if (renderType === 'cards') {
                    container.innerHTML = '<div class="no-appointments">No upcoming appointments</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (renderType === 'table') {
                container.innerHTML = '<tr><td colspan="5">Failed to load appointments</td></tr>';
            } else if (renderType === 'cards') {
                container.innerHTML = '<div class="error">Failed to load appointments</div>';
            }
        });
}

function renderAppointmentsTable(appointments, container) {
    let rows = '';
    appointments.forEach(appt => {
        // Format date
        const date = new Date(appt.appointment_date);
        const formattedDate = date.toLocaleDateString('en-US', { 
            weekday: 'short', 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        // Format time
        const timeString = appt.appointment_time;
        const formattedTime = timeString.includes(':') 
            ? timeString.substring(0, 5)  // Extract HH:MM
            : timeString;  // Use as-is

        // Determine status class
        let statusClass = '';
        let statusText = appt.status;
        switch(appt.status.toLowerCase()) {
            case 'confirmed':
                statusClass = 'status-confirmed';
                statusText = 'Confirmed';
                break;
            case 'pending':
                statusClass = 'status-pending';
                statusText = 'Pending';
                break;
            case 'canceled':
            case 'cancelled':
                statusClass = 'status-canceled';
                statusText = 'Canceled';
                break;
            default:
                statusClass = 'status-pending';
        }
        
        rows += `
            <tr>
                <td>${formattedDate} at ${formattedTime}</td>
                <td>${appt.doctor_name}</td>
                <td>${appt.specialty || 'General'}</td>
                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td>
                    <button class="btn btn-secondary" onclick="rescheduleAppointment(${appt.id})">Reschedule</button>
                    <button class="btn btn-primary" onclick="startCall(${appt.id}, '${appt.doctor_name}')">Join Call</button>
                    <button class="btn btn-danger" onclick="deleteAppointment(${appt.id}, '${container.id}')">Delete</button>
                </td>
            </tr>
        `;
    });
    
    container.innerHTML = rows;
}

function renderAppointmentsCards(appointments, container) {
    let cardsHTML = '';
    appointments.forEach(appt => {
        const date = new Date(appt.appointment_date);
        const formattedDate = date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric'
        });
        
        // Format time
        const timeString = appt.appointment_time;
        const formattedTime = timeString.includes(':') 
            ? timeString.substring(0, 5)  // Extract HH:MM
            : timeString;  // Use as-is

        let statusClass = '';
        let statusText = '';
        switch(appt.status.toLowerCase()) {
            case 'confirmed':
                statusClass = 'status-upcoming';
                statusText = 'Upcoming';
                break;
            case 'pending':
                statusClass = 'status-pending';
                statusText = 'Pending';
                break;
            case 'canceled':
            case 'cancelled':
                statusClass = 'status-canceled';
                statusText = 'Canceled';
                break;
            default:
                statusClass = 'status-pending';
                statusText = 'Pending';
        }

        cardsHTML += `
            <div class="appointment-card">
                <div class="appointment-header">
                    <div class="appointment-time">${formattedTime}</div>
                    <div class="appointment-status ${statusClass}">${statusText}</div>
                </div>
                <div class="appointment-doctor">
                    <div class="doctor-avatar-small">
                        ${appt.doctor_name.charAt(0)}
                    </div>
                    <div class="doctor-info-small">
                        <h4>${appt.doctor_name}</h4>
                        <p>${appt.specialty || appt.specialty || 'General Medicine'}</p>
                    </div>
                </div>
                <div class="appointment-date">
                    <i class="far fa-calendar"></i> ${formattedDate}
                </div>
                <div class="appointment-actions">
                    <button class="btn btn-secondary" onclick="viewAppointmentDetails(${appt.id})">
                        Details
                    </button>
                    <button class="btn btn-primary" onclick="startCall(${appt.id}, '${appt.doctor_name}')">
                        Start Call
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = cardsHTML;
}

function deleteAppointment(appointmentId, containerId) {
    if (!confirm('Are you sure you want to delete this appointment?')) return;
    
    fetch('../../backend/delete_appointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${appointmentId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Appointment deleted successfully', 'success');
            // Determine render type based on container ID
            const renderType = containerId === 'appointments-container' ? 'cards' : 'table';
            loadAppointments(containerId, renderType);
            loadAppointmentCount();
        } else {
            showNotification(`Error: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to delete appointment', 'error');
    });
}

/* ================ VIDEO PAGE ================ */
function initVideoPage() {
    loadAppointments('appointments-container', 'cards');
    setupEventListeners();
    getDevices();
    checkActiveCall();
}

// Event listener setup
function setupEventListeners() {
    const controls = [
        ['mic-toggle', toggleMic],
        ['camera-toggle', toggleCamera],
        ['end-call', endCall],
        ['screen-share', toggleScreenShare]
    ];

    controls.forEach(([id, handler]) => {
        document.getElementById(id)?.addEventListener('click', handler);
    });
}

// Active call restoration
function checkActiveCall() {
    const activeCall = localStorage.getItem('activeCall');
    
    if (activeCall) {
        const appointmentData = JSON.parse(activeCall);
        startCall(appointmentData.id, appointmentData.doctor);
    }
}

// Device management
async function getDevices() {
    try {
        const devices = await AgoraRTC.getDevices();
        // Handle device selection UI
    } catch (error) {
        console.error('Device detection failed:', error);
        showNotification('Could not access camera/microphone. Please check permissions.', 'error');
    }
}

// UI Update Helpers ---------------------------------------------------------
function updateConnectionStatus(status, text) {
    const statusEl = document.getElementById('connection-status');
    statusEl.className = `status-indicator ${status}`;
    document.getElementById('connection-text').textContent = text;
}

function updateButton(buttonId, active, iconOn, iconOff) {
    const btn = document.getElementById(buttonId);
    if (!btn) return;
    
    btn.classList.toggle('active', active);
    btn.innerHTML = `<i class="fas fa-${active ? iconOn : iconOff}"></i>`;
}

function showRemotePlaceholder(doctorName) {
    const placeholder = document.getElementById('remote-placeholder');
    if (!placeholder) return;
    
    placeholder.innerHTML = `
        <i class="fas fa-user-md"></i>
        <h3>${doctorName ? `Connecting to Dr. ${doctorName.split(' ')[1]}` : 'Waiting for Doctor'}</h3>
        <p>${doctorName ? 'Please wait while we connect you...' : 'Your doctor will appear here once they join'}</p>
    `;
}

// Core Call Functionality ---------------------------------------------------
async function startCall(appointmentId, doctorName) {
    try {
        currentAppointment = appointmentId;
        
        // Save active call state
        localStorage.setItem('activeCall', JSON.stringify({
            id: appointmentId,
            doctor: doctorName
        }));

        // UI Initialization
        showRemotePlaceholder(doctorName);
        updateConnectionStatus('connecting', 'Connecting...');
        document.getElementById('local-placeholder').innerHTML = '<i class="fas fa-user"></i>';

        await initializeAgora(appointmentId);
        await setupLocalTracks();
        
        updateConnectionStatus('connected', 'Connected');
        startCallTimer();
        showNotification(`Connected to Dr. ${doctorName.split(' ')[1]}`, 'success');
    } catch (error) {
        console.error("Call setup failed:", error);
        showNotification('Connection failed. Please try again.', 'error');
        endCall();
    }
}

async function initializeAgora(channelName) {
    agoraClient = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
    
    // Event handlers
    agoraClient.on("user-published", handleUserPublished);
    agoraClient.on("user-unpublished", handleUserUnpublished);
    
    // Join channel
    const AGORA_APP_ID = "a3d932a4e8ce4b9180a19a7672b8108b";
    await agoraClient.join(AGORA_APP_ID, channelName, null);
}

async function setupLocalTracks() {
    [localTracks.audioTrack, localTracks.videoTrack] = await Promise.all([
        AgoraRTC.createMicrophoneAudioTrack(),
        AgoraRTC.createCameraVideoTrack()
    ]);

    const localPlaceholder = document.getElementById('local-placeholder');
    if (localPlaceholder) localPlaceholder.innerHTML = '';
    
    localTracks.videoTrack.play('local-video');
    await agoraClient.publish([localTracks.audioTrack, localTracks.videoTrack]);
}

// Track Management ----------------------------------------------------------
async function handleUserPublished(user, mediaType) {
    try {
        await agoraClient.subscribe(user, mediaType);
        const uid = user.uid.toString();
        remoteUsers[uid] = user;

        if (mediaType === 'video') {
            const remotePlaceholder = document.getElementById('remote-placeholder');
            if (remotePlaceholder) remotePlaceholder.innerHTML = '';
            
            const player = document.createElement('div');
            player.id = `remote-player-${uid}`;
            player.className = 'video-player';
            document.getElementById('remote-video').appendChild(player);
            user.videoTrack.play(player.id);
        }
        
        if (mediaType === 'audio') {
            user.audioTrack.play();
        }
    } catch (error) {
        console.error("Error handling remote user:", error);
    }
}

function handleUserUnpublished(user) {
    const uid = user.uid.toString();
    const playerEl = document.getElementById(`remote-player-${uid}`);
    if (playerEl) playerEl.remove();
    delete remoteUsers[uid];
}

// Control Functions ---------------------------------------------------------
function toggleMic() {
    if (!localTracks.audioTrack) return;
    
    const newState = !localTracks.audioTrack.enabled;
    localTracks.audioTrack.setEnabled(newState);
    
    updateButton('mic-toggle', newState, 'microphone', 'microphone-slash');
    showNotification(newState ? 'Microphone active' : 'Microphone muted', newState ? 'success' : 'info');
}

function toggleCamera() {
    if (!localTracks.videoTrack) return;
    
    const newState = !localTracks.videoTrack.enabled;
    localTracks.videoTrack.setEnabled(newState);
    
    updateButton('camera-toggle', newState, 'video', 'video-slash');
    showNotification(newState ? 'Camera active' : 'Camera turned off', newState ? 'success' : 'info');
}

async function toggleScreenShare() {
    const screenBtn = document.getElementById('screen-share');
    if (!screenBtn) return;

    if (localTracks.screenTrack) {
        await stopScreenSharing();
        screenBtn.classList.remove('active');
        screenBtn.innerHTML = '<i class="fas fa-desktop"></i>';
        return;
    }

    try {
        screenBtn.classList.add('active');
        screenBtn.innerHTML = '<i class="fas fa-stop"></i>';
        
        localTracks.screenTrack = await AgoraRTC.createScreenVideoTrack({ 
            encoderConfig: '1080p_1',
            optimizationMode: 'detail'
        });
        
        await agoraClient.publish(localTracks.screenTrack);
        document.getElementById('local-placeholder').innerHTML = '';
        localTracks.screenTrack.play('local-video');
        
        // Pause camera track to save bandwidth
        if (localTracks.videoTrack) {
            localTracks.videoTrack.stop();
            document.getElementById('local-video').innerHTML = '';
        }
        
        showNotification('Screen sharing started', 'success');
    } catch (error) {
        console.error("Screen sharing failed:", error);
        screenBtn.classList.remove('active');
        screenBtn.innerHTML = '<i class="fas fa-desktop"></i>';
        
        if (error.message.includes('permission')) {
            showNotification('Please allow screen sharing permissions', 'error');
        } else {
            showNotification('Screen sharing not supported in this browser', 'error');
        }
    }
}

async function stopScreenSharing() {
    if (!localTracks.screenTrack) return;
    
    await agoraClient.unpublish(localTracks.screenTrack);
    localTracks.screenTrack.stop();
    localTracks.screenTrack.close();
    localTracks.screenTrack = null;

    // Restore camera track
    if (localTracks.videoTrack) {
        document.getElementById('local-placeholder').innerHTML = '';
        localTracks.videoTrack.play('local-video');
    }
    
    showNotification('Screen sharing stopped', 'info');
}

// Call Lifecycle Management -------------------------------------------------
async function endCall() {
    try {
        // Stop all tracks
        if (localTracks.audioTrack) {
            localTracks.audioTrack.stop();
            localTracks.audioTrack.close();
        }
        if (localTracks.videoTrack) {
            localTracks.videoTrack.stop();
            localTracks.videoTrack.close();
        }
        if (localTracks.screenTrack) {
            localTracks.screenTrack.stop();
            localTracks.screenTrack.close();
        }
        
        if (agoraClient) {
            await agoraClient.leave();
        }
    } catch (error) {
        console.error("Error ending call:", error);
    } finally {
        resetCallUI();
        clearAgoraResources();
        localStorage.removeItem('activeCall');
        showNotification('Call ended', 'info');
    }
}

function resetCallUI() {
    // Timer cleanup
    if (callTimer) {
        clearInterval(callTimer);
        callTimer = null;
    }
    
    const timerDisplay = document.getElementById('timer-display');
    if (timerDisplay) timerDisplay.style.display = 'none';
    
    // Reset UI states
    updateConnectionStatus('disconnected', 'Disconnected');
    showRemotePlaceholder();
    
    // Reset control buttons
    updateButton('mic-toggle', false, 'microphone', 'microphone-slash');
    updateButton('camera-toggle', false, 'video', 'video-slash');
    
    const screenBtn = document.getElementById('screen-share');
    if (screenBtn) {
        screenBtn.classList.remove('active');
        screenBtn.innerHTML = '<i class="fas fa-desktop"></i>';
    }
    
    // Reset video placeholders
    const localPlaceholder = document.getElementById('local-placeholder');
    if (localPlaceholder) localPlaceholder.innerHTML = '<i class="fas fa-user"></i>';
    
    const remoteVideo = document.getElementById('remote-video');
    if (remoteVideo) remoteVideo.innerHTML = `
        <div class="video-placeholder" id="remote-placeholder">
            <i class="fas fa-user-md"></i>
            <h3>Call Ended</h3>
            <p>Your consultation has completed</p>
        </div>
    `;
}

function clearAgoraResources() {
    agoraClient = null;
    localTracks = {
        audioTrack: null,
        videoTrack: null,
        screenTrack: null
    };
    remoteUsers = {};
    currentAppointment = null;
}

// Timer Functionality -------------------------------------------------------
function startCallTimer() {
    callDuration = 0;
    const timerDisplay = document.getElementById('timer-display');
    if (!timerDisplay) return;
    
    timerDisplay.style.display = 'block';
    timerDisplay.textContent = '00:00';
    
    callTimer = setInterval(() => {
        callDuration++;
        const minutes = Math.floor(callDuration / 60).toString().padStart(2, '0');
        const seconds = (callDuration % 60).toString().padStart(2, '0');
        timerDisplay.textContent = `${minutes}:${seconds}`;
    }, 1000);
}

/* ================ LABS PAGE ================ */
function initLabsPage() {
    // Fetch lab data from backend
    fetchLabData();
    
    // File upload functionality
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('file-input');
    
    if (uploadArea && fileInput) {
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#2a86ff';
            uploadArea.style.backgroundColor = 'rgba(42, 134, 255, 0.05)';
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '';
            uploadArea.style.backgroundColor = '';
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '';
            uploadArea.style.backgroundColor = '';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                showNotification('File selected: ' + e.dataTransfer.files[0].name, 'success');
            }
        });
        
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                showNotification('File selected: ' + fileInput.files[0].name, 'success');
            }
        });
        
        // Upload button with backend integration
        document.getElementById('upload-external-btn').addEventListener('click', function() {
            const testType = document.getElementById('external-test-type').value;
            const testDate = document.getElementById('external-test-date').value;
            const facility = document.getElementById('external-facility').value;
            
            if (!fileInput.files.length) {
                showNotification('Please select a file to upload', 'error');
                return;
            }
            
            if (!testType) {
                showNotification('Please enter test type', 'error');
                return;
            }
            
            if (!testDate) {
                showNotification('Please select test date', 'error');
                return;
            }
            
            // Show loading
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            this.disabled = true;
            
            const formData = new FormData();
            formData.append('test_name', testType);
            formData.append('test_date', testDate);
            formData.append('facility', facility);
            formData.append('result_file', fileInput.files[0]);
            
            fetch('../../backend/upload_external_lab.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Results uploaded successfully!', 'success');
                    // Refresh lab data
                    fetchLabData();
                } else {
                    showNotification(data.message || 'Upload failed', 'error');
                }
            })
            .catch(error => {
                showNotification('Upload failed: ' + error.message, 'error');
            })
            .finally(() => {
                this.innerHTML = 'Upload Results';
                this.disabled = false;
                fileInput.value = '';
                document.getElementById('external-test-type').value = '';
                document.getElementById('external-test-date').value = '';
                document.getElementById('external-facility').value = '';
            });
        });
    }
    
    // Test report selection
    const reportSelect = document.getElementById('test-report-select');
    if (reportSelect) {
        reportSelect.addEventListener('change', function() {
            if (this.value) {
                loadLabResultDetails(this.value);
            }
        });
    }
    
    // Request new test with backend integration
    document.getElementById('request-test-btn')?.addEventListener('click', function() {
        const testType = document.getElementById('test-type-request').value;
        const testName = document.getElementById('test-name-request').value;
        const reason = document.getElementById('test-reason').value;
        const doctor = document.getElementById('doctor-request').value;
        
        if (!testType || !testName) {
            showNotification('Test type and name are required', 'error');
            return;
        }
        
        // Show loading
        const btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        btn.disabled = true;
        
        fetch('../../backend/request_lab_test.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                test_type: testType,
                test_name: testName,
                reason: reason,
                doctor_id: doctor
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Test request submitted successfully', 'success');
                // Clear form
                document.getElementById('test-type-request').value = '';
                document.getElementById('test-name-request').value = '';
                document.getElementById('test-reason').value = '';
                document.getElementById('doctor-request').value = '';
                // Refresh lab data
                fetchLabData();
            } else {
                showNotification(data.message || 'Request failed', 'error');
            }
        })
        .catch(error => {
            showNotification('Request failed: ' + error.message, 'error');
        })
        .finally(() => {
            btn.innerHTML = 'Submit Request';
            btn.disabled = false;
        });
    });
    
    // Save notification settings with backend integration
    window.saveNotificationSettings = function() {
        const email = document.getElementById('email-notification').checked;
        const sms = document.getElementById('sms-notification').checked;
        const app = document.getElementById('app-notification').checked;
        const frequency = document.getElementById('notification-frequency').value;
        
        // Show loading
        showNotification('Saving notification preferences...', 'info');
        
        fetch('../../backend/save_notification_settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                sms: sms,
                app: app,
                frequency: frequency
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Notification preferences saved successfully', 'success');
            } else {
                showNotification(data.message || 'Save failed', 'error');
            }
        })
        .catch(error => {
            showNotification('Save failed: ' + error.message, 'error');
        });
    }
}

// Fetch all lab data from backend
function fetchLabData() {
    fetch('../../backend/get_lab_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateLabHistory(data.data.history);
                populateLabResults(data.data.results);
                populateLabOrders(data.data.orders);
                populateNotificationSettings(data.data.notifications);
                populateDoctorDropdown(data.data.doctors);
                populateReportDropdown(data.data.results);
            } else {
                showNotification('Failed to load lab data: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load lab data', 'error');
        });
}

// Helper functions to populate lab data
function populateLabHistory(history) {
    const container = document.querySelector('.lab-test-history');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (history.length === 0) {
        container.innerHTML = '<p>No lab history found</p>';
        return;
    }
    
    history.forEach(test => {
        const date = new Date(test.order_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        
        const statusClass = test.status === 'completed' ? 'status-completed' : 
                           test.status === 'scheduled' ? 'status-scheduled' : 'status-pending';
        
        const item = document.createElement('div');
        item.className = 'lab-history-item';
        item.innerHTML = `
            <div class="lab-history-name">${test.test_name}</div>
            <div class="lab-history-date">${date}</div>
            <div class="lab-status-badge ${statusClass}">${test.status}</div>
        `;
        container.appendChild(item);
    });
}

function populateLabOrders(orders) {
    const container = document.querySelector('.lab-orders-list');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (orders.length === 0) {
        container.innerHTML = '<p>No upcoming lab orders</p>';
        return;
    }
    
    orders.forEach(order => {
        const scheduledDate = order.scheduled_date ? 
            new Date(order.scheduled_date).toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }) : 'Not scheduled';
        
        const statusClass = order.status === 'scheduled' ? 'status-scheduled' : 'status-pending';
        
        const item = document.createElement('div');
        item.className = 'lab-order-item';
        item.innerHTML = `
            <div class="order-header">
                <h4>${order.test_name}</h4>
                <div class="lab-status-badge ${statusClass}">${order.status}</div>
            </div>
            <div class="order-details">
                ${order.scheduled_date ? `
                    <p><i class="fas fa-calendar-alt"></i> ${scheduledDate}</p>
                    <p><i class="fas fa-map-marker-alt"></i> ${order.facility || 'Not specified'}</p>
                ` : ''}
                ${order.doctor_name ? `
                    <p><i class="fas fa-user-md"></i> Ordered by ${order.doctor_name}</p>
                ` : ''}
            </div>
            ${order.instructions ? `
                <div class="order-instructions">
                    <p><strong>Instructions:</strong> ${order.instructions}</p>
                </div>
            ` : ''}
            <div class="order-actions">
                <button class="btn btn-secondary">
                    <i class="fas fa-calendar"></i> Reschedule
                </button>
                ${order.status === 'scheduled' ? `
                    <button class="btn btn-primary">
                        <i class="fas fa-directions"></i> Get Directions
                    </button>
                ` : `
                    <button class="btn btn-primary" onclick="scheduleLabTest(${order.id})">
                        <i class="fas fa-calendar-plus"></i> Schedule Test
                    </button>
                `}
            </div>
        `;
        container.appendChild(item);
    });
}

function populateLabResults(results) {
    const tableBody = document.querySelector('.data-table tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    if (results.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7">No lab results found</td></tr>';
        return;
    }
    
    results.forEach(result => {
        const date = new Date(result.result_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        
        const statusClass = result.status === 'completed' ? 'status-completed' : 'status-pending';
        const resultStatus = result.interpretation ? 
            (result.interpretation.toLowerCase().includes('normal') ? 'normal' : 
             result.interpretation.toLowerCase().includes('elevated') ? 'warning' : 'danger') : 'pending';
        
        const statusText = resultStatus === 'normal' ? 'Normal' : 
                          resultStatus === 'warning' ? 'Abnormal' : 'Danger';
                          
        const statusClassMap = {
            normal: 'normal',
            warning: 'warning',
            danger: 'danger'
        };
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${result.test_name}</td>
            <td>${date}</td>
            <td>Blood Test</td>
            <td><div class="lab-status-badge ${statusClass}">${result.status}</div></td>
            <td>${result.doctor_name || 'Pending Review'}</td>
            <td>
                ${result.interpretation ? `
                    <div class="result-status ${statusClassMap[resultStatus]}">${statusText}</div>
                ` : '--'}
            </td>
            <td>
                ${result.interpretation ? `
                    <button class="btn btn-secondary" onclick="viewResult(${result.id})">View</button>
                    <button class="btn btn-primary" onclick="downloadResult(${result.id})">Download</button>
                    <button class="btn btn-danger" onclick="initiateDoctorChat(${result.doctor_id})">Talk to Doctor</button>
                ` : `
                    <button class="btn btn-secondary" disabled>View</button>
                `}
            </td>
        `;
        tableBody.appendChild(row);
    });
}

function populateReportDropdown(results) {
    const select = document.getElementById('test-report-select');
    if (!select) return;
    
    select.innerHTML = '<option value="">Select a test to view</option>';
    
    results.forEach(result => {
        const date = new Date(result.result_date).toLocaleDateString();
        const option = document.createElement('option');
        option.value = result.id;
        option.textContent = `${result.test_name} (${date})`;
        select.appendChild(option);
    });
}

function populateNotificationSettings(settings) {
    if (!settings) return;
    
    document.getElementById('email-notification').checked = !!settings.email_notification;
    document.getElementById('sms-notification').checked = !!settings.sms_notification;
    document.getElementById('app-notification').checked = !!settings.app_notification;
    document.getElementById('notification-frequency').value = settings.frequency || 'immediate';
}

function populateDoctorDropdown(doctors) {
    const select = document.getElementById('doctor-request');
    if (!select) return;
    
    select.innerHTML = '<option value="">Select doctor</option>';
    doctors.forEach(doctor => {
        const option = document.createElement('option');
        option.value = doctor.id;
        option.textContent = doctor.name;
        select.appendChild(option);
    });
}

function loadLabResultDetails(resultId) {
    fetch(`../../backend/get_lab_result.php?result_id=${resultId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayLabResultModal(data.data);
            } else {
                showNotification('Failed to load result details', 'error');
            }
        })
        .catch(error => {
            showNotification('Error loading result: ' + error.message, 'error');
        });
}

function displayLabResultModal(data) {
    const result = data.result;
    const items = data.items;
    
    document.getElementById('modal-test-name').textContent = result.test_name;
    document.getElementById('modal-test-date').textContent = new Date(result.result_date).toLocaleDateString();
    document.getElementById('modal-test-doctor').textContent = result.doctor_name || 'Not reviewed';
    document.getElementById('modal-test-facility').textContent = result.facility || 'Not specified';
    document.getElementById('modal-doctor-remarks').textContent = result.interpretation || 'No interpretation available';
    
    const tbody = document.getElementById('modal-result-values');
    tbody.innerHTML = '';
    
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4">No result items available</td></tr>';
        return;
    }
    
    items.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.test_component}</td>
            <td>${item.value} ${item.unit || ''}</td>
            <td>${item.normal_range || 'N/A'}</td>
            <td><div class="result-status ${item.status}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</div></td>
        `;
        if (item.status !== 'normal') {
            row.classList.add('warning');
        }
        tbody.appendChild(row);
    });
    
    document.getElementById('test-result-modal').style.display = 'flex';
}

// Update viewResult to use real IDs
function viewResult(resultId) {
    loadLabResultDetails(resultId);
}

function downloadResult(resultId) {
    // In a real implementation, this would download the file
    showNotification('Downloading lab result...', 'info');
}

// Update scheduleLabTest to work with backend
function scheduleLabTest(orderId) {
    showNotification('Scheduling lab test...', 'info');
    
    // For demo purposes, we'll simulate scheduling
    setTimeout(() => {
        showNotification('Lab test scheduled successfully', 'success');
        // Refresh lab orders
        fetchLabData();
    }, 1500);
}

function initiateDoctorChat(doctorId) {
    showNotification('Connecting you with a doctor...', 'info');
    // Simulate connection
    setTimeout(() => {
        showNotification('You are now connected with a doctor', 'success');
    }, 2000);
}

/* ================ UTILITY FUNCTIONS ================ */
function showNotification(message, type = 'info', duration = 3000) {
    // Remove existing notifications
    const existing = document.querySelector('.app-notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `app-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">${message}</div>
        <button class="close-btn">&times;</button>
    `;
    
    notification.querySelector('.close-btn').addEventListener('click', () => {
        notification.remove();
    });
    
    document.body.appendChild(notification);
    
    // Auto-hide after duration
    if (duration > 0) {
        setTimeout(() => {
            if (document.body.contains(notification)) {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }
}

function rescheduleAppointment(id) {
    showNotification(`Rescheduling appointment #${id} - feature coming soon!`, 'info');
}

function joinCall(id) {
    showNotification(`Joining call for appointment #${id} - feature coming soon!`, 'info');
}

function viewAppointmentDetails(id) {
    showNotification(`Viewing details for appointment #${id}`, 'info');
}

function formatDate(dateString) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Handle browser back/forward
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.page) {
        const page = event.state.page;
        const url = `${page}.php`;
        
        // Update active state
        document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
            item.classList.remove('active');
            const link = item.querySelector('a');
            if (link && link.getAttribute('data-content').includes(page)) {
                item.classList.add('active');
            }
        });
        loadAppointmentCount();
        
        loadMainContent(url, page, false);
    }
});
    </script>
    <script src="Dashboard/js/AgoraRTC_N-production.esm.mjs"></script>
</body>
</html>