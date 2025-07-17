<?php
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// Initialize errors and form data
$errors = $_SESSION['errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['errors']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediClinic - Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
          :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #93c5fd;
            --secondary: #4fd1c5;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.25rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--gray-900);
            line-height: 1.5;
        }
        
        /* Header with modern glass effect */
        .header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 1.2rem 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            transition: var(--transition);
        }
        
        .logo:hover {
            transform: translateY(-2px);
        }
        
        .logo i {
            font-size: 2rem;
            transition: transform 0.5s ease;
        }
        
        .logo:hover i {
            transform: rotate(15deg);
        }
        
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            flex: 1;
            width: 100%;
        }
        
        /* Create container with glass effect and subtle animation */
        .create-container {
            display: flex;
            gap: 3rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            animation: fadeIn 0.6s ease-out;
            transform-origin: center;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Hero section with animated gradient */
        .create-hero {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            animation: gradientAnimation 15s ease infinite;
            background-size: 200% 200%;
        }
        
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .create-hero::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
            animation: rotateAnimation 30s linear infinite;
        }
        
        @keyframes rotateAnimation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            position: relative;
            z-index: 1;
            max-width: 90%;
            opacity: 0.95;
        }
        
        .hero-features {
            list-style: none;
            position: relative;
            z-index: 1;
        }
        
        .hero-features li {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1rem;
            font-weight: 500;
            transition: var(--transition);
            padding: 0.5rem;
            border-radius: var(--radius-sm);
        }
        
        .hero-features li:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .hero-features i {
            background: rgba(255, 255, 255, 0.2);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .hero-features li:hover i {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Form section with modern inputs */
        .create-form-section {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
        }
        
        .form-title {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: var(--gray-900);
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .form-subtitle {
            color: var(--gray-700);
            margin-bottom: 2rem;
        }
        
        .required-field {
            color: var(--error);
            font-weight: 600;
        }
        
        .create-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-weight: 500;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .input-container {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--gray-50);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            background: white;
        }
        
        .form-input:not(:placeholder-shown) {
            background: white;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-700);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        /* Create button with modern animation */
        .create-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--radius-md);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .create-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .create-btn:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .create-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(30deg);
            transition: var(--transition);
        }
        
        .create-btn:hover::after {
            transform: translateX(100%) rotate(30deg);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }
        
        .login-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
            transition: var(--transition);
            position: relative;
            padding-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .login-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }
        
        .login-link:hover {
            color: var(--primary-dark);
        }
        
        .login-link:hover::after {
            width: 100%;
        }
        
        /* Error messages with animation */
        .error-messages {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--error);
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            animation: slideIn 0.3s ease-out;
            display: none;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .error-message {
            color: var(--error);
            margin-bottom: 0.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .error-message i {
            font-size: 1.1rem;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Password requirements with modern styling */
        .password-requirements {
            background: var(--gray-100);
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-top: 0.5rem;
            border: 1px solid var(--gray-200);
        }
        
        .requirements-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .requirements-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 0.5rem;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--gray-700);
            transition: var(--transition);
        }
        
        .requirement-item.valid {
            color: var(--success);
        }
        
        .requirement-item.valid i {
            animation: bounce 0.5s;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .requirement-item.invalid {
            color: var(--gray-700);
        }
        
        /* Footer styling */
        .footer {
            background: white;
            padding: 1.5rem 2rem;
            margin-top: auto;
            text-align: center;
            color: var(--gray-700);
            font-size: 0.9rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 0.8rem;
            flex-wrap: wrap;
        }
        
        .footer-link {
            color: var(--gray-700);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }
        
        .footer-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--gray-700);
            transition: var(--transition);
        }
        
        .footer-link:hover {
            color: var(--primary);
        }
        
        .footer-link:hover::after {
            width: 100%;
        }
        
        /* Hospital dropdown styling */
        .hospital-group {
            background: var(--gray-50);
            border-radius: var(--radius-md);
            padding: 1rem;
            border: 1px dashed var(--primary-light);
            margin-top: 0.5rem;
        }
        
        /* Success message styling */
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success);
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            animation: slideIn 0.3s ease-out;
            display: none;
        }
        
        .success-message-content {
            color: var(--success);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Responsive design */
        @media (max-width: 900px) {
            .create-container {
                flex-direction: column;
            }
            
            .create-hero {
                padding: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .logo {
                justify-content: center;
            }
        }
        
        @media (max-width: 600px) {
            .main-content {
                padding: 0 1rem;
            }
            
            .create-form-section, .create-hero {
                padding: 1.5rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        /* Role selection cards */
        .role-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .role-card {
            background: var(--gray-100);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }
        
        .role-card.selected {
            background: rgba(37, 99, 235, 0.1);
            border-color: var(--primary);
        }
        
        .role-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
        
        .role-card h3 {
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }
        
        .role-card p {
            font-size: 0.8rem;
            color: var(--gray-500);
        }
        
        /* Loading spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Animation for hospital section */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-down {
            animation: slideDown 0.4s ease-out;
        }
        
        .hospital-icon {
            color: var(--primary);
            margin-right: 8px;
        }
        
        .not-robot {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .country-residence {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <i class="fas fa-heartbeat"></i>
            <span>MediClinic</span>
        </div>
    </header>

    <div class="main-content">
        <div id="errorMessages" class="error-messages"></div>
        
        <div id="successMessage" class="success-message">
            <div class="success-message-content">
                <i class="fas fa-check-circle"></i>
                <span id="successText"></span>
            </div>
        </div>

        <div class="create-container">
            <div class="create-hero">
                <h1 class="hero-title">Join MediClinic Today</h1>
                <p class="hero-subtitle">Create your account to access healthcare services or professional opportunities.</p>
                <ul class="hero-features">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>24/7 access to healthcare services</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Connect with medical professionals</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Secure medical records management</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Personalized health insights</span>
                    </li>
                </ul>
            </div>
            
            <div class="create-form-section">
                <h2 class="form-title">Create Your Account</h2>
                <p class="form-subtitle">Select your role and complete your details</p>
                
                <div class="role-selection">
                    <div class="role-card" data-role="patient">
                        <i class="fas fa-user-injured"></i>
                        <h3>Patient</h3>
                        <p>Healthcare Services</p>
                    </div>
                    <div class="role-card" data-role="doctor">
                        <i class="fas fa-user-md"></i>
                        <h3>Doctor</h3>
                        <p>Medical Professional</p>
                    </div>
                    <div class="role-card" data-role="nurse">
                        <i class="fas fa-user-nurse"></i>
                        <h3>Nurse</h3>
                        <p>Healthcare Provider</p>
                    </div>
                    <div class="role-card" data-role="admin">
                        <i class="fas fa-user-cog"></i>
                        <h3>Administrator</h3>
                        <p>System Management</p>
                    </div>
                    <div class="role-card" data-role="hospital">
                        <i class="fas fa-hospital"></i>
                        <h3>Healthcare Facility</h3>
                        <p>Hospital/Clinic</p>
                    </div>
                </div>
                
                <form id="createAccountForm" class="create-form" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" id="userType" name="userType" value="patient">
                    
                    <!-- Doctor Registration Group -->
                    <div id="doctorRegistrationGroup" class="form-group" style="display: none;">
                        <div style="width: 100%;">
                            <label for="doctorRegistration">
                                <i class="fas fa-id-card"></i> 
                                Medical License Number <span class="required-field">*</span>
                            </label>
                            <input type="text" id="doctorRegistration" name="doctorRegistration" 
                                   class="form-input" autocomplete="off" placeholder="MD-123456"
                                   value="<?php echo isset($formData['doctorRegistration']) ? htmlspecialchars($formData['doctorRegistration']) : ''; ?>">
                            <div class="hint-text"><small>Format: MD-123456 (2 letters, hyphen, 6 digits)</small></div>
                        </div>
                    </div>
                    
                    <!-- Nurse Registration Group -->
                    <div id="nurseRegistrationGroup" class="form-group" style="display: none;">
                        <div style="width: 100%;">
                            <label for="nurseRegistration">
                                <i class="fas fa-id-card"></i> 
                                Nursing License Number <span class="required-field">*</span>
                            </label>
                            <input type="text" id="nurseRegistration" name="nurseRegistration" 
                                   class="form-input" autocomplete="off" placeholder="RN-123456"
                                   value="<?php echo isset($formData['nurseRegistration']) ? htmlspecialchars($formData['nurseRegistration']) : ''; ?>">
                            <div class="hint-text"><small>Format: RN-123456 (2 letters, hyphen, 6 digits)</small></div>
                        </div>
                    </div>
                    
                    <!-- Hospital Registration Group -->
                    <div id="hospitalRegistrationGroup" class="form-group" style="display: none;">
                        <div style="width: 100%;">
                            <label for="hospitalName">
                                <i class="fas fa-hospital"></i> 
                                Facility Name <span class="required-field">*</span>
                            </label>
                            <input type="text" id="hospitalName" name="hospitalName" class="form-input" 
                                   autocomplete="off" placeholder="MediClinic General Hospital">
                        </div>
                        <div style="margin-top: 1rem;">
                            <label for="hospitalRegistration">
                                <i class="fas fa-id-card"></i> 
                                Facility Registration Number <span class="required-field">*</span>
                            </label>
                            <input type="text" id="hospitalRegistration" name="hospitalRegistration" class="form-input" 
                                   autocomplete="off" placeholder="HOSP-123456">
                            <div class="hint-text"><small>Format: HOSP-123456 (4 letters, hyphen, 6 digits)</small></div>
                        </div>
                    </div>
                    
                    <!-- Specialty Group -->
                    <div id="specialtyGroup" class="form-group" style="display: none;">
                        <div style="width: 100%;">
                            <label for="specialty">
                                <i class="fas fa-stethoscope"></i> 
                                Medical Specialty <span class="required-field">*</span>
                            </label>
                            <select id="specialty" name="specialty" class="form-input">
                                <option value="">- Select Specialty -</option>
                                <option value="Cardiology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                                <option value="Dermatology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                                <option value="Endocrinology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Endocrinology') ? 'selected' : ''; ?>>Endocrinology</option>
                                <option value="Gastroenterology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Gastroenterology') ? 'selected' : ''; ?>>Gastroenterology</option>
                                <option value="Neurology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                                <option value="Oncology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Oncology') ? 'selected' : ''; ?>>Oncology</option>
                                <option value="Ophthalmology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Ophthalmology') ? 'selected' : ''; ?>>Ophthalmology</option>
                                <option value="Pediatrics" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Pediatrics') ? 'selected' : ''; ?>>Pediatrics</option>
                                <option value="Psychiatry" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Psychiatry') ? 'selected' : ''; ?>>Psychiatry</option>
                                <option value="Radiology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Radiology') ? 'selected' : ''; ?>>Radiology</option>
                                <option value="Surgery" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
                                <option value="Urology" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Urology') ? 'selected' : ''; ?>>Urology</option>
                                <option value="Other" <?php echo (isset($formData['specialty']) && $formData['specialty'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Hospital Selection Group -->
                    <div id="hospitalGroup" class="form-group" style="display: none;">
                        <div class="hospital-group slide-down">
                            <div style="width: 100%;">
                                <label for="hospital">
                                    <i class="fas fa-hospital hospital-icon"></i> 
                                    Hospital Affiliation <span class="required-field">*</span>
                                </label>
                                <select id="hospital" name="hospital" class="form-input">
                                    <option value="">- Select Hospital -</option>
                                    <option value="1" <?php echo (isset($formData['hospital']) && $formData['hospital'] === '1') ? 'selected' : ''; ?>>MediClinic General Hospital</option>
                                    <option value="2" <?php echo (isset($formData['hospital']) && $formData['hospital'] === '2') ? 'selected' : ''; ?>>City Health Center</option>
                                    <option value="3" <?php echo (isset($formData['hospital']) && $formData['hospital'] === '3') ? 'selected' : ''; ?>>University Medical Center</option>
                                    <option value="4" <?php echo (isset($formData['hospital']) && $formData['hospital'] === '4') ? 'selected' : ''; ?>>Regional Hospital</option>
                                    <option value="5" <?php echo (isset($formData['hospital']) && $formData['hospital'] === '5') ? 'selected' : ''; ?>>Community Clinic</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> 
                            Email Address <span class="required-field">*</span>
                        </label>
                        <input type="email" id="email" name="email" required class="form-input" 
                               autocomplete="off" placeholder="your@email.com"
                               value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="retypeEmail">
                            <i class="fas fa-envelope"></i> 
                            Retype Email Address <span class="required-field">*</span>
                        </label>
                        <input type="email" id="retypeEmail" name="retypeEmail" required class="form-input" 
                               autocomplete="off" placeholder="retype@email.com"
                               value="<?php echo isset($formData['retypeEmail']) ? htmlspecialchars($formData['retypeEmail']) : ''; ?>">
                        <p id="emailMatchError" class="error-message" style="display:none; margin-top: 5px;">
                            <i class="fas fa-exclamation-circle"></i> Emails do not match
                        </p>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> 
                            Password <span class="required-field">*</span>
                        </label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" required class="form-input" 
                                   autocomplete="new-password" placeholder="Create your password">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        
                        <div class="password-requirements">
                            <div class="requirements-title">
                                <i class="fas fa-shield-alt"></i> Password Requirements:
                            </div>
                            <ul class="requirements-list">
                                <li class="requirement-item" id="passwordLength">
                                    <i class="fas fa-circle"></i>
                                    <span>8-16 characters</span>
                                </li>
                                <li class="requirement-item" id="passwordUpper">
                                    <i class="fas fa-circle"></i>
                                    <span>Upper & lowercase letters</span>
                                </li>
                                <li class="requirement-item" id="passwordNumber">
                                    <i class="fas fa-circle"></i>
                                    <span>At least one number</span>
                                </li>
                                <li class="requirement-item" id="passwordSpecial">
                                    <i class="fas fa-circle"></i>
                                    <span>Special character</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="retypePassword">
                            <i class="fas fa-lock"></i> 
                            Retype Password <span class="required-field">*</span>
                        </label>
                        <div class="input-container">
                            <input type="password" id="retypePassword" name="retypePassword" required class="form-input" 
                                   autocomplete="new-password" placeholder="Confirm your password">
                            <button type="button" class="password-toggle" id="toggleRetypePassword">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <p id="passwordMatchError" class="error-message" style="display:none; margin-top: 5px;">
                            <i class="fas fa-exclamation-circle"></i> Passwords do not match
                        </p>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="firstName">
                                <i class="fas fa-user"></i> 
                                First Name <span class="required-field">*</span>
                            </label>
                            <input type="text" id="firstName" name="firstName" required class="form-input" 
                                   autocomplete="off" placeholder="John"
                                   value="<?php echo isset($formData['firstName']) ? htmlspecialchars($formData['firstName']) : ''; ?>">
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="lastName">
                                <i class="fas fa-user"></i> 
                                Last Name <span class="required-field">*</span>
                            </label>
                            <input type="text" id="lastName" name="lastName" required class="form-input" 
                                   autocomplete="off" placeholder="Doe"
                                   value="<?php echo isset($formData['lastName']) ? htmlspecialchars($formData['lastName']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="countryCode">
                                <i class="fas fa-globe"></i> 
                                Country Code <span class="required-field">*</span>
                            </label>
                            <select id="countryCode" name="countryCode" required class="form-input">
                                <option value="">- Select -</option>
                                <option value="+1" <?php echo (isset($formData['countryCode']) && $formData['countryCode'] === '+1') ? 'selected' : ''; ?>>United States (+1)</option>
                                <option value="+44" <?php echo (isset($formData['countryCode']) && $formData['countryCode'] === '+44') ? 'selected' : ''; ?>>United Kingdom (+44)</option>
                                <option value="+91" <?php echo (isset($formData['countryCode']) && $formData['countryCode'] === '+91') ? 'selected' : ''; ?>>India (+91)</option>
                                <option value="+254" <?php echo (isset($formData['countryCode']) && $formData['countryCode'] === '+254') ? 'selected' : ''; ?>>Kenya (+254)</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="flex: 2;">
                            <label for="phoneNumber">
                                <i class="fas fa-phone"></i> 
                                Phone Number <span class="required-field">*</span>
                            </label>
                            <input type="text" id="phoneNumber" name="phoneNumber" required class="form-input" 
                                   autocomplete="off" placeholder="123-456-7890"
                                   value="<?php echo isset($formData['phoneNumber']) ? htmlspecialchars($formData['phoneNumber']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group country-residence">
                        <label for="countryResidence">
                            <i class="fas fa-flag"></i> 
                            Country of Residence <span class="required-field">*</span>
                        </label>
                        <select id="countryResidence" name="countryResidence" required class="form-input">
                            <option value="">- Select Country -</option>
                            <option value="USA" <?php echo (isset($formData['countryResidence']) && $formData['countryResidence'] === 'USA') ? 'selected' : ''; ?>>United States</option>
                            <option value="UK" <?php echo (isset($formData['countryResidence']) && $formData['countryResidence'] === 'UK') ? 'selected' : ''; ?>>United Kingdom</option>
                            <option value="India" <?php echo (isset($formData['countryResidence']) && $formData['countryResidence'] === 'India') ? 'selected' : ''; ?>>India</option>
                            <option value="Kenya" <?php echo (isset($formData['countryResidence']) && $formData['countryResidence'] === 'Kenya') ? 'selected' : ''; ?>>Kenya</option>
                            <option value="Other" <?php echo (isset($formData['countryResidence']) && $formData['countryResidence'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">
                            <i class="fas fa-calendar"></i> 
                            Date of Birth <span class="required-field">*</span>
                        </label>
                        <input type="date" id="dob" name="dob" required class="form-input"
                               value="<?php echo isset($formData['dob']) ? htmlspecialchars($formData['dob']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">
                            <i class="fas fa-venus-mars"></i> 
                            Gender <span class="required-field">*</span>
                        </label>
                        <select id="gender" name="gender" required class="form-input">
                            <option value="">- Select Gender -</option>
                            <option value="male" <?php echo (isset($formData['gender']) && $formData['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (isset($formData['gender']) && $formData['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo (isset($formData['gender']) && $formData['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                            <option value="prefer-not" <?php echo (isset($formData['gender']) && $formData['gender'] === 'prefer-not') ? 'selected' : ''; ?>>Prefer not to say</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <label><i class="fas fa-bell"></i> Notification Preferences:</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                                <label style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="checkbox" id="jobNotifications" name="jobNotifications" class="form-checkbox" checked>
                                    <span>Job notifications</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="checkbox" id="careerOpportunities" name="careerOpportunities" class="form-checkbox" checked>
                                    <span>Career opportunities</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="not-robot">
                            <input type="checkbox" id="notRobot" name="notRobot" required style="width: auto;">
                            <label for="notRobot" style="font-size: 0.9rem;">
                                I confirm I am not a robot
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="font-weight: 500;">
                            <i class="fas fa-file-contract"></i> 
                            Terms of Use <span class="required-field">*</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                            <input type="checkbox" id="termsAccepted" name="termsAccepted" required style="width: auto;">
                            <label for="termsAccepted" style="font-size: 0.9rem;">
                                I accept the <a href="#" style="color: var(--primary);">Terms & Conditions</a>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" id="createAccountBtn" class="create-btn">
                        <i class="fas fa-user-plus"></i> 
                        <span>Create Account</span>
                        <div class="spinner" id="submitSpinner"></div>
                    </button>
                    
                    <div class="form-footer">
                        <a href="login.php" class="login-link">
                            <i class="fas fa-sign-in-alt"></i> Already have an account? Sign in
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2023 MediClinic. All rights reserved.</p>
        <div class="footer-links">
            <a href="#" class="footer-link">Privacy Policy</a>
            <a href="#" class="footer-link">Terms of Service</a>
            <a href="#" class="footer-link">Contact Us</a>
            <a href="#" class="footer-link">About MediClinic</a>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get DOM elements
            const roleCards = document.querySelectorAll('.role-card');
            const userTypeInput = document.getElementById('userType');
            const doctorGroup = document.getElementById('doctorRegistrationGroup');
            const nurseGroup = document.getElementById('nurseRegistrationGroup');
            const hospitalRegGroup = document.getElementById('hospitalRegistrationGroup');
            const specialtyGroup = document.getElementById('specialtyGroup');
            const hospitalGroup = document.getElementById('hospitalGroup');
            const togglePassword = document.getElementById('togglePassword');
            const toggleRetypePassword = document.getElementById('toggleRetypePassword');
            const passwordInput = document.getElementById('password');
            const retypePasswordInput = document.getElementById('retypePassword');
            const emailInput = document.getElementById('email');
            const retypeEmailInput = document.getElementById('retypeEmail');
            const form = document.getElementById('createAccountForm');
            const errorMessages = document.getElementById('errorMessages');
            const passwordMatchError = document.getElementById('passwordMatchError');
            const emailMatchError = document.getElementById('emailMatchError');
            const submitBtn = document.getElementById('createAccountBtn');
            const spinner = document.getElementById('submitSpinner');
            const successMessage = document.getElementById('successMessage');
            const successText = document.getElementById('successText');
            
            // Password requirement elements
            const passwordLength = document.getElementById('passwordLength');
            const passwordUpper = document.getElementById('passwordUpper');
            const passwordNumber = document.getElementById('passwordNumber');
            const passwordSpecial = document.getElementById('passwordSpecial');
            
            // Role selection
            roleCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    roleCards.forEach(c => c.classList.remove('selected'));
                    
                    // Add selected class to clicked card
                    this.classList.add('selected');
                    
                    // Get selected role
                    const role = this.getAttribute('data-role');
                    userTypeInput.value = role;
                    
                    // Reset and hide all role-specific fields
                    doctorGroup.style.display = 'none';
                    nurseGroup.style.display = 'none';
                    hospitalRegGroup.style.display = 'none';
                    specialtyGroup.style.display = 'none';
                    hospitalGroup.style.display = 'none';
                    
                    // Show relevant fields based on role
                    if (role === 'doctor') {
                        doctorGroup.style.display = 'flex';
                        specialtyGroup.style.display = 'flex';
                        hospitalGroup.style.display = 'flex';
                    } else if (role === 'nurse') {
                        nurseGroup.style.display = 'flex';
                        hospitalGroup.style.display = 'flex';
                    } else if (role === 'admin') {
                        hospitalGroup.style.display = 'flex';
                    } else if (role === 'hospital') {
                        hospitalRegGroup.style.display = 'flex';
                    }
                });
            });
            
            // Set patient as default
            roleCards[0].classList.add('selected');
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
            });
            
            toggleRetypePassword.addEventListener('click', function() {
                const type = retypePasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                retypePasswordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
            });
            
            // Real-time password validation
            passwordInput.addEventListener('input', validatePassword);
            retypePasswordInput.addEventListener('input', validatePasswordMatch);
            emailInput.addEventListener('input', validateEmailMatch);
            retypeEmailInput.addEventListener('input', validateEmailMatch);
            
            // Validate password requirements
            function validatePassword() {
                const password = passwordInput.value;
                
                // Length check
                if (password.length >= 8 && password.length <= 16) {
                    passwordLength.classList.add('valid');
                    passwordLength.classList.remove('invalid');
                } else {
                    passwordLength.classList.remove('valid');
                    passwordLength.classList.add('invalid');
                }
                
                // Uppercase and lowercase check
                if (/[A-Z]/.test(password) && /[a-z]/.test(password)) {
                    passwordUpper.classList.add('valid');
                    passwordUpper.classList.remove('invalid');
                } else {
                    passwordUpper.classList.remove('valid');
                    passwordUpper.classList.add('invalid');
                }
                
                // Number check
                if (/\d/.test(password)) {
                    passwordNumber.classList.add('valid');
                    passwordNumber.classList.remove('invalid');
                } else {
                    passwordNumber.classList.remove('valid');
                    passwordNumber.classList.add('invalid');
                }
                
                // Special character check
                if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                    passwordSpecial.classList.add('valid');
                    passwordSpecial.classList.remove('invalid');
                } else {
                    passwordSpecial.classList.remove('valid');
                    passwordSpecial.classList.add('invalid');
                }
                
                // Validate password match
                validatePasswordMatch();
            }
            
            // Validate password match
            function validatePasswordMatch() {
                if (passwordInput.value && retypePasswordInput.value && 
                    passwordInput.value === retypePasswordInput.value) {
                    passwordMatchError.style.display = 'none';
                } else if (retypePasswordInput.value) {
                    passwordMatchError.style.display = 'block';
                }
            }
            
            // Validate email match
            function validateEmailMatch() {
                if (emailInput.value && retypeEmailInput.value && 
                    emailInput.value === retypeEmailInput.value) {
                    emailMatchError.style.display = 'none';
                } else if (retypeEmailInput.value) {
                    emailMatchError.style.display = 'block';
                }
            }
            
            // AJAX form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Disable button and show spinner
                submitBtn.disabled = true;
                spinner.style.display = 'inline-block';
                
                try {
                    // Create FormData object
                    const formData = new FormData(form);
                    
                    // Send POST request
                    const response = await fetch('backend/process_form.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Show success message
                        successText.textContent = 'Account created successfully!';
                        successMessage.style.display = 'block';
                        
                        // Hide any previous errors
                        errorMessages.style.display = 'none';
                        
                        // Reset form after 2 seconds
                        setTimeout(() => {
                            form.reset();
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        // Show errors
                        errorMessages.innerHTML = result.errors.map(error => 
                            `<div class="error-message"><i class="fas fa-exclamation-circle"></i> ${error}</div>`
                        ).join('');
                        errorMessages.style.display = 'block';
                        
                        // Hide success message if any
                        successMessage.style.display = 'none';
                    }
                } catch (error) {
                    // Network error handling
                    errorMessages.innerHTML = 
                        `<div class="error-message"><i class="fas fa-exclamation-circle"></i> Network error occurred. Please try again.</div>`;
                    errorMessages.style.display = 'block';
                } finally {
                    // Re-enable button and hide spinner
                    submitBtn.disabled = false;
                    spinner.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 