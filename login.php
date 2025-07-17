<?php
session_start();


// Initialize variables
$login_errors = $_SESSION['login_errors'] ?? [];
$formData = $_SESSION['formData'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['formData']); // Clear after retrieval

// Set default form values
$identifier = $formData['identifier'] ?? '';
$notRobotChecked = isset($formData['notRobot']) ? 'checked' : '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediClinic - Healthcare Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #4fd1c5;
            --success: #10b981;
            --error: #ef4444;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-700: #374151;
            --gray-900: #111827;
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
            color: var(--gray-900);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 1.2rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .logo i {
            font-size: 2rem;
        }
        
        .login-switch {
            background: var(--success);
            color: white;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }
        
        .login-switch:hover {
            background: #0da271;
        }
        
        .login-container {
            display: flex;
            gap: 3rem;
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin: 2rem 0;
        }
        
        .login-hero {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-hero::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            position: relative;
            z-index: 1;
            max-width: 80%;
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
        }
        
        .hero-features i {
            background: rgba(255, 255, 255, 0.2);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-form-section {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
        }
        
        .form-title {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: var(--gray-900);
        }
        
        .form-subtitle {
            color: var(--gray-700);
            margin-bottom: 2rem;
        }
        
        .required-field {
            color: var(--error);
            font-weight: 600;
        }
        
        .login-form {
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
        }
        
        .input-container {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 3rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-700);
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
        }
        
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--gray-100);
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--gray-300);
        }
        
        .captcha-checkbox {
            width: 24px;
            height: 24px;
            accent-color: var(--primary);
        }
        
        .captcha-label {
            font-size: 0.9rem;
            color: var(--gray-700);
        }
        
        .captcha-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }
        
        .captcha-logo img {
            width: 70px;
            height: 35px;
            object-fit: cover;
        }
        
        .captcha-text {
            font-size: 0.7rem;
            color: var(--gray-700);
        }
        
        .login-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .login-btn:hover {
            background: var(--primary-dark);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }
        
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }
        
        .register-section {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
            text-align: center;
        }
        
        .register-text {
            color: var(--gray-700);
            margin-bottom: 0.8rem;
        }
        
        .register-link {
            display: inline-block;
            background: var(--success);
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .register-link:hover {
            background: #0da271;
        }
        
        .error-messages {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--error);
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0 0.5rem 0.5rem 0;
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
        }
        
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
            transition: color 0.2s;
        }
        
        .footer-link:hover {
            color: var(--primary);
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            background: transparent;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-700);
            transition: all 0.3s;
            position: relative;
        }
        
        .tab.active {
            color: var(--primary);
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Responsive Styles */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-hero {
                padding: 2rem;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .logo {
                margin-bottom: 1rem;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                width: 100%;
                text-align: left;
                border-bottom: 1px solid var(--gray-200);
            }
        }
        
        @media (max-width: 768px) {
            .login-form-section, 
            .login-hero {
                padding: 2rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .form-footer {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 0 1rem;
            }
            
            .login-form-section, 
            .login-hero {
                padding: 1.5rem;
            }
            
            .hero-title {
                font-size: 1.8rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
            
            .captcha-container {
                flex-wrap: wrap;
            }
            
            .footer-links {
                gap: 1rem;
            }
        }
        
        /* Employee specific */
        .employee .login-switch {
            background: var(--primary);
        }
        
        .employee .login-switch:hover {
            background: var(--primary-dark);
        }
        
        .create-account-link {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .create-account-link:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <i class="fas fa-heartbeat"></i>
            <span>MediClinic</span>
        </div>
        <a href="#" id="switch-login" class="login-switch">
            <i class="fas fa-user-md"></i> Staff Login
        </a>
    </header>

    <div class="container">
        <!-- Tabs for switching between forms -->
        <div class="tabs">
            <button class="tab active" data-tab="patient">Patient Login</button>
            <button class="tab" data-tab="employee">Staff Login</button>
        </div>
        
        <!-- Error messages -->
        <?php if (!empty($login_errors)): ?>
            <div class="error-messages">
                <?php foreach ($login_errors as $error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="error-messages" id="error-container" style="display: none;">
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error-text"></span>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Patient Login -->
        <div class="tab-content active" id="patient-tab">
            <div class="login-container">
                <div class="login-hero">
                    <h1 class="hero-title">Patient Care Portal</h1>
                    <p class="hero-subtitle">Manage your health records and appointments</p>
                    <ul class="hero-features">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Access medical records</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Schedule appointments</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Communicate with your care team</span>
                        </li>
                    </ul>
                </div>
                
                <div class="login-form-section">
                    <h2 class="form-title">Patient Sign In</h2>
                    <p class="form-subtitle">Access your personal health portal</p>
                    
                    <form class="login-form" id="patient-form">
                        <input type="hidden" name="form_type" value="patient">
                        <input type="hidden" name="csrf_token" value="<?= isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '' ?>">
                        <div class="form-group">
                            <label for="patient-email">
                                Email Address <span class="required-field">*</span>
                            </label>
                            <div class="input-container">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="patient-email" name="email" class="form-input" 
                                       placeholder="your.email@example.com" 
                                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="patient-password">
                                Password <span class="required-field">*</span>
                            </label>
                            <div class="input-container">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="patient-password" name="password" class="form-input" 
                                       placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" id="togglePatientPassword">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="captcha-container">
                                <input type="checkbox" id="patient-notRobot" name="notRobot" class="captcha-checkbox" 
                                    <?= isset($formData['notRobot']) ? 'checked' : '' ?> required>
                                <label for="patient-notRobot" class="captcha-label">I'm not a robot</label>
                                <div class="captcha-logo">
                                    <img src="https://placehold.co/80x40/f0f0f0/333333?text=reCAPTCHA" alt="reCAPTCHA">
                                    <div class="captcha-text">Privacy - Terms</div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Patient Sign In
                        </button>
                        
                        <div class="form-footer">
                            <a href="#" class="forgot-password">Forgot your password?</a>
                            <div class="role-info">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    First time user? <a href="createaccount.php">Create account</a>
                                </small>
                            </div>
                        </div>
                    </form>
                    
                    <div class="register-section">
                        <p class="register-text">Not registered yet?</p>
                        <a href="createaccount.php" class="register-link">
                            <i class="fas fa-user-plus"></i> Create Patient Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Employee Login -->
        <div class="tab-content" id="employee-tab">
            <div class="login-container">
                <div class="login-hero">
                    <h1 class="hero-title">MediClinic Employee Portal</h1>
                    <p class="hero-subtitle">Access your professional dashboard and manage patient care</p>
                    <ul class="hero-features">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Medical records access</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Patient scheduling tools</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Clinical decision support</span>
                        </li>
                    </ul>
                </div>
                
                <div class="login-form-section">
                    <h2 class="form-title">Employee Sign In</h2>
                    <p class="form-subtitle">Use your professional credentials</p>
                    
                    <form class="login-form" id="employee-form">
                        <input type="hidden" name="form_type" value="employee">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '' ?>">  
                        <div class="form-group">
                            <label for="employee-identifier">License Number <span class="required-field">*</span></label>
                            <div class="input-container">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="employee-identifier" name="identifier" class="form-input" 
                                       placeholder="MD-123456 or RN-123456" 
                                       value="<?= htmlspecialchars($identifier) ?>" 
                                       required>
                            </div>
                            <div class="hint-text">
                                <small>Format: MD-123456 for doctors, RN-123456 for nurses</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="employee-password">Password <span class="required-field">*</span></label>
                            <div class="input-container">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="employee-password" name="password" class="form-input"
                                       placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" id="toggleEmployeePassword">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="captcha-container">
                                <input type="checkbox" id="employee-notRobot" name="notRobot" class="captcha-checkbox" 
                                    <?= $notRobotChecked ?> required>
                                <label for="employee-notRobot" class="captcha-label">I'm not a robot</label>
                                <div class="captcha-logo">
                                    <img src="https://placehold.co/80x40/f0f0f0/333333?text=reCAPTCHA" alt="reCAPTCHA">
                                    <div class="captcha-text">Privacy - Terms</div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Employee Sign In
                        </button>
                        
                        <div class="form-footer">
                            <a href="#" class="forgot-password">Forgot your password?</a>
                            <div class="role-info">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    First time user? <a href="createaccount.php" class="create-account-link">Create account</a>
                                </small>
                            </div>
                        </div>
                    </form>
                    
                    <div class="register-section">
                        <p class="register-text">Don't have an employee account?</p>
                        <a href="createaccount.php" class="register-link">
                            <i class="fas fa-user-plus"></i> Create Employee Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> MediClinic. All rights reserved.</p>
        <div class="footer-links">
            <a href="#" class="footer-link">Privacy Policy</a>
            <a href="#" class="footer-link">Terms of Service</a>
            <a href="#" class="footer-link">Patient Support</a>
            <a href="#" class="footer-link">Employee Resources</a>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            function setupPasswordToggle(toggleId, inputId) {
                const togglePassword = document.getElementById(toggleId);
                const passwordInput = document.getElementById(inputId);
                
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    this.innerHTML = type === 'password' ? 
                        '<i class="far fa-eye"></i>' : 
                        '<i class="far fa-eye-slash"></i>';
                });
            }
            
            // Set up both password toggles
            setupPasswordToggle('togglePatientPassword', 'patient-password');
            setupPasswordToggle('toggleEmployeePassword', 'employee-password');
            
            // Tab switching functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    const tabId = `${tab.dataset.tab}-tab`;
                    document.getElementById(tabId).classList.add('active');
                    
                    // Update header button
                    const switchButton = document.getElementById('switch-login');
                    if (tab.dataset.tab === 'patient') {
                        switchButton.innerHTML = '<i class="fas fa-user-md"></i> Staff Login';
                        document.body.classList.remove('employee');
                    } else {
                        switchButton.innerHTML = '<i class="fas fa-user-injured"></i> Patient Login';
                        document.body.classList.add('employee');
                    }
                });
            });
            
            // Header button functionality
            document.getElementById('switch-login').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                if (this.innerHTML.includes('Staff')) {
                    // Switch to employee login
                    document.querySelector('.tab[data-tab="employee"]').classList.add('active');
                    document.getElementById('employee-tab').classList.add('active');
                    this.innerHTML = '<i class="fas fa-user-injured"></i> Patient Login';
                    document.body.classList.add('employee');
                } else {
                    // Switch to patient login
                    document.querySelector('.tab[data-tab="patient"]').classList.add('active');
                    document.getElementById('patient-tab').classList.add('active');
                    this.innerHTML = '<i class="fas fa-user-md"></i> Staff Login';
                    document.body.classList.remove('employee');
                }
            });
            
            // Form submission handling
            function showError(message) {
                const errorContainer = document.getElementById('error-container');
                const errorText = document.getElementById('error-text');
                
                errorText.textContent = message;
                errorContainer.style.display = 'block';
                
                // Auto-hide error after 5 seconds
                setTimeout(() => {
                    errorContainer.style.display = 'none';
                }, 5000);
            }
            
            // Handle form submission to backend
            function handleFormSubmission(formId, backendUrl) {
                const form = document.getElementById(formId);
                
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Show loading indicator
                    const submitBtn = form.querySelector('.login-btn');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    submitBtn.disabled = true;
                    
                    try {
                        const formData = new FormData(form);
                        
                        const response = await fetch(backendUrl, {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Redirect to dashboard on success
                            window.location.href = data.redirect;
                        } else {
                            // Show errors
                            const errorContainer = document.getElementById('error-container');
                            errorContainer.innerHTML = '';
                            
                            data.errors.forEach(error => {
                                const errorElement = document.createElement('div');
                                errorElement.className = 'error-message';
                                errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${error}`;
                                errorContainer.appendChild(errorElement);
                            });
                            
                            errorContainer.style.display = 'block';
                            
                            // Scroll to error container
                            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showError('An unexpected error occurred. Please try again.');
                    } finally {
                        // Restore button state
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    }
                });
            }
            
            // Set up form submissions
            handleFormSubmission('patient-form', 'backend/process_login.php');
            handleFormSubmission('employee-form', 'backend/process_login.php');
        });
    </script>
</body>
</html>