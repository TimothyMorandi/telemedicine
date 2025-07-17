<?php
// backend/process_login.php
session_start();
require_once 'config.php';

// Enable detailed errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Define constants for validation
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5 minutes in seconds

// Verify database connection
if (!isset($pdo)) { 
    echo json_encode([
        'success' => false,
        'errors' => ['Database connection failed. Please contact support.']
    ]);
    exit();
}

// Initialize login attempts counter if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

// Check if user is locked out
if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    
    if ($time_since_last_attempt < LOCKOUT_TIME) {
        $remaining_time = LOCKOUT_TIME - $time_since_last_attempt;
        echo json_encode([
            'success' => false,
            'errors' => ['Too many login attempts. Please try again in ' . ceil($remaining_time / 60) . ' minutes.']
        ]);
        exit();
    } else {
        // Reset attempts if lockout time has passed
        $_SESSION['login_attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $errors = [];
        $formData = [];
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
        }
        
        // Determine login type
        $form_type = $_POST['form_type'] ?? '';
        $identifier = '';
        $password = $_POST['password'] ?? '';
        $notRobot = isset($_POST['notRobot']) ? 1 : 0;
        
        // Store form data for repopulation
        if ($form_type === 'patient') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $identifier = $email;
            $formData['email'] = $email;
        } elseif ($form_type === 'employee') {
            $identifier = trim($_POST['identifier'] ?? '');
            $formData['identifier'] = $identifier;
        }
        
        $formData['notRobot'] = $notRobot;
        
        // Validate required fields
        if (empty($identifier)) {
            $errors[] = $form_type === 'patient' ? 'Email is required.' : 'License number is required.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }
        
        if (!$notRobot) {
            $errors[] = 'Please confirm you are not a robot.';
        }
        
        // Validate employee identifier format
        if ($form_type === 'employee' && empty($errors)) {
            $identifierPattern = '/^(MD|RN)-\d{6}$/';
            if (!preg_match($identifierPattern, $identifier)) {
                $errors[] = 'Invalid license format. Use MD-123456 or RN-123456.';
            }
        }
        
        // Increment login attempts
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
        
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['formData'] = $formData;
            
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit();
        }
        
        // Attempt to authenticate user
        $user = null;
        
        if ($form_type === 'patient') {
            // Patient login by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND user_type = 'patient'");
            $stmt->execute([$identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($form_type === 'employee') {
            // Employee login by registration number
            $stmt = $pdo->prepare("SELECT * FROM users WHERE 
                (doctor_registration = ? OR nurse_registration = ?) 
                AND user_type IN ('doctor', 'nurse', 'admin')");
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Verify user exists and password
        if (!$user) {
            $errors[] = $form_type === 'patient' 
                ? 'Invalid email or password.' 
                : 'Invalid license number or password.';
            
            $_SESSION['login_errors'] = $errors;
            $_SESSION['formData'] = $formData;
            
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit();
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            $errors[] = 'Invalid credentials. Please try again.';
            
            $_SESSION['login_errors'] = $errors;
            $_SESSION['formData'] = $formData;
            
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit();
        }
        
        // Check if account is active
        if (isset($user['status']) && $user['status'] !== 'active') {
            $errors[] = 'Your account is inactive. Please contact support.';
            
            $_SESSION['login_errors'] = $errors;
            $_SESSION['formData'] = $formData;
            
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit();
        }
        
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        
        // Reset login attempts
        $_SESSION['login_attempts'] = 0;
        
        // Log successful login (with directory creation)
        $logDir = __DIR__ . '/../logs';
        $logFile = $logDir . '/logins.log';
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true); // Create directory if missing
        }
        
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . ucfirst($user['user_type']) . " logged in: " . $user['email'] . " (ID: " . $user['id'] . ")\n";
        @file_put_contents($logFile, $logMessage, FILE_APPEND); // Silent failure

        // Determine redirect based on user type
        $redirect = '';
        switch ($user['user_type']) {
            case 'patient':
                    $redirect = 'Dashboard/navs/dash_sider.php';
                    break;
            case 'doctor':
                $redirect = 'Dashboard2/navs/dash_sider.php';
                break;
            case 'nurse':
                $redirect = '../nurse_dashboard.php';
                break;
            case 'admin':
                $redirect = '../admin_dashboard.php';
                break;
            case 'hospital':
                $redirect = '../hospital_dashboard.php';
                break;
            default:
                $redirect = '../dashboard.php';
        }
        
        echo json_encode([
            'success' => true,
            'redirect' => $redirect
        ]);
        exit();
        
    } catch (Exception $e) {
        error_log("Login Process Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        
        echo json_encode([
            'success' => false,
            'errors' => ['A system error occurred. Please try again later.']
        ]);
        exit();
    }
} else {
    echo json_encode([
        'success' => false,
        'errors' => ['Invalid request method.']
    ]);
    exit();
}