<?php
// Start output buffering to prevent accidental output
ob_start();

session_start();
require_once 'config.php';

// Prevent any accidental output
ini_set('display_errors', 0);
error_reporting(0);

// Set content type to JSON
header('Content-Type: application/json');

// Define constants for validation patterns
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 16);
define('DOCTOR_REGISTRATION_PATTERN', '/^MD-\d{6}$/');
define('NURSE_REGISTRATION_PATTERN', '/^RN-\d{6}$/');
define('HOSPITAL_REGISTRATION_PATTERN', '/^HOSP-\d{6}$/');
define('PHONE_DIGIT_MIN', 7);
define('PHONE_DIGIT_MAX', 15);
define('NAME_MAX_LENGTH', 50);
define('REGISTRATION_MAX_LENGTH', 20);
define('HOSPITAL_NAME_MAX_LENGTH', 100);

// Allowed user types
$allowedUserTypes = ['patient', 'doctor', 'nurse', 'admin', 'hospital'];

// Verify database connection
if (!isset($pdo)) { 
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'errors' => ['Database connection failed. Please contact support.']
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $errors = [];

        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
        }

        // Sanitize and normalize userType
        $userType = strtolower(trim($_POST['userType'] ?? ''));
        if (!in_array($userType, $allowedUserTypes)) {
            $errors[] = 'Invalid user type selected.';
        }

        // Sanitize inputs
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $retypeEmail = filter_input(INPUT_POST, 'retypeEmail', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $retypePassword = $_POST['retypePassword'] ?? '';
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $countryCode = trim($_POST['countryCode'] ?? '');
        $phoneNumber = trim($_POST['phoneNumber'] ?? '');
        $countryResidence = trim($_POST['countryResidence'] ?? '');
        $dob = $_POST['dob'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $doctorRegistration = isset($_POST['doctorRegistration']) ? strtoupper(trim($_POST['doctorRegistration'])) : '';
        $nurseRegistration = isset($_POST['nurseRegistration']) ? strtoupper(trim($_POST['nurseRegistration'])) : '';
        $hospitalRegistration = isset($_POST['hospitalRegistration']) ? strtoupper(trim($_POST['hospitalRegistration'])) : '';
        $hospitalName = isset($_POST['hospitalName']) ? trim($_POST['hospitalName']) : '';
        $specialty = isset($_POST['specialty']) ? trim($_POST['specialty']) : null;
        $hospital = isset($_POST['hospital']) ? (int)trim($_POST['hospital']) : null; // Cast to integer
        $jobNotifications = isset($_POST['jobNotifications']) ? 1 : 0;
        $careerOpportunities = isset($_POST['careerOpportunities']) ? 1 : 0;
        $notRobot = isset($_POST['notRobot']) ? 1 : 0;
        $termsAccepted = isset($_POST['termsAccepted']) ? 1 : 0;

        // Validate required fields
        $requiredFields = [
            'User type' => $userType,
            'Email' => $email,
            'Password' => $password,
            'First name' => $firstName,
            'Last name' => $lastName,
            'Country code' => $countryCode,
            'Phone number' => $phoneNumber,
            'Country of residence' => $countryResidence,
            'Date of birth' => $dob,
            'Gender' => $gender,
            'Not robot confirmation' => $notRobot,
            'Terms acceptance' => $termsAccepted
        ];
        
        foreach ($requiredFields as $field => $value) {
            if (empty($value)) {
                $errors[] = "$field is required.";
            }
        }

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        if ($email !== $retypeEmail) {
            $errors[] = 'Email addresses do not match.';
        }

        // Password validation
        $passwordErrors = [];
        if ($password !== $retypePassword) {
            $passwordErrors[] = 'Passwords do not match.';
        }
        if (strlen($password) < PASSWORD_MIN_LENGTH || strlen($password) > PASSWORD_MAX_LENGTH) {
            $passwordErrors[] = 'Password must be 8-16 characters.';
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
            $passwordErrors[] = 'Password must contain both uppercase and lowercase letters.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $passwordErrors[] = 'Password must contain at least one number.';
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $passwordErrors[] = 'Password must contain at least one special character.';
        }
        if (preg_match('/\s/', $password)) {
            $passwordErrors[] = 'Password must not contain spaces.';
        }
        $errors = array_merge($errors, $passwordErrors);

        // Name validation
        if (strlen($firstName) > NAME_MAX_LENGTH) {
            $errors[] = 'First name exceeds maximum length.';
        }
        if (strlen($lastName) > NAME_MAX_LENGTH) {
            $errors[] = 'Last name exceeds maximum length.';
        }

        // Phone number validation
        $phoneDigits = preg_replace('/[^0-9]/', '', $phoneNumber);
        $digitCount = strlen($phoneDigits);
        if ($digitCount < PHONE_DIGIT_MIN || $digitCount > PHONE_DIGIT_MAX) {
            $errors[] = "Phone number must contain between " . PHONE_DIGIT_MIN . " and " . PHONE_DIGIT_MAX . " digits.";
        }

        // Role-specific validation
        if (in_array($userType, $allowedUserTypes)) {
            switch ($userType) {
                case 'doctor':
                    if (empty($doctorRegistration)) {
                        $errors[] = 'Doctor registration number is required.';
                    } elseif (strlen($doctorRegistration) > REGISTRATION_MAX_LENGTH) {
                        $errors[] = 'Doctor registration number is too long.';
                    } elseif (!preg_match(DOCTOR_REGISTRATION_PATTERN, $doctorRegistration)) {
                        $errors[] = 'Invalid doctor registration format. Use MD-123456 format.';
                    }
                    if (empty($specialty)) {
                        $errors[] = 'Medical specialty is required for doctors.';
                    }
                    // Add hospital validation for doctors
                    if (empty($hospital)) {
                        $errors[] = 'Hospital selection is required for doctors.';
                    }
                    break;

                case 'nurse':
                    if (empty($nurseRegistration)) {
                        $errors[] = 'Nurse registration number is required.';
                    } elseif (strlen($nurseRegistration) > REGISTRATION_MAX_LENGTH) {
                        $errors[] = 'Nurse registration number is too long.';
                    } elseif (!preg_match(NURSE_REGISTRATION_PATTERN, $nurseRegistration)) {
                        $errors[] = 'Invalid nurse registration format. Use RN-123456 format.';
                    }
                    // Add hospital validation for nurses
                    if (empty($hospital)) {
                        $errors[] = 'Hospital selection is required for nurses.';
                    }
                    break;
                    
                case 'admin':
                    // Add hospital validation for admins
                    if (empty($hospital)) {
                        $errors[] = 'Hospital selection is required for administrators.';
                    }
                    break;

                case 'hospital':
                    if (empty($hospitalName)) {
                        $errors[] = 'Facility name is required.';
                    } elseif (strlen($hospitalName) > HOSPITAL_NAME_MAX_LENGTH) {
                        $errors[] = 'Facility name is too long.';
                    }
                    if (empty($hospitalRegistration)) {
                        $errors[] = 'Facility registration number is required.';
                    } elseif (strlen($hospitalRegistration) > REGISTRATION_MAX_LENGTH) {
                        $errors[] = 'Facility registration number is too long.';
                    } elseif (!preg_match(HOSPITAL_REGISTRATION_PATTERN, $hospitalRegistration)) {
                        $errors[] = 'Invalid facility registration format. Use HOSP-123456 format.';
                    }
                    break;
            }
        }

        // Final email check to prevent race conditions
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email already registered. Please try signing in.';
            }
        }

        // Check for duplicate registration numbers
        if (empty($errors)) {
            switch ($userType) {
                case 'doctor':
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE doctor_registration = ?");
                    $stmt->execute([$doctorRegistration]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Doctor registration number already exists.';
                    }
                    break;
                case 'nurse':
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE nurse_registration = ?");
                    $stmt->execute([$nurseRegistration]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Nurse registration number already exists.';
                    }
                    break;
                case 'hospital':
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE hospital_registration = ?");
                    $stmt->execute([$hospitalRegistration]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Facility registration number already exists.';
                    }
                    break;
            }
        }

        if (empty($errors)) {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare data for insertion
            $data = [
                'user_type' => $userType,
                'email' => $email,
                'password' => $hashedPassword,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'country_code' => $countryCode,
                'phone_number' => $phoneNumber,
                'country_residence' => $countryResidence,
                'dob' => $dob,
                'gender' => $gender,
                'job_notifications' => $jobNotifications,
                'career_opportunities' => $careerOpportunities,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Add role-specific fields
            switch ($userType) {
                case 'doctor':
                    $data['doctor_registration'] = $doctorRegistration;
                    $data['specialty'] = $specialty;
                    $data['hospital_id'] = $hospital;
                    break;
                case 'nurse':
                    $data['nurse_registration'] = $nurseRegistration;
                    $data['hospital_id'] = $hospital;
                    break;
                case 'admin':
                    $data['hospital_id'] = $hospital;
                    break;
                case 'hospital':
                    $data['hospital_registration'] = $hospitalRegistration;
                    $data['hospital_name'] = $hospitalName;
                    break;
            }

            // Build SQL query
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO users ($columns) VALUES ($placeholders)";
            
            // DEBUG: Log SQL and data
            error_log("SQL: $sql");
            error_log("Data: " . print_r($data, true));
            
            // Execute query with enhanced error handling
            $stmt = $pdo->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("SQL prepare failed: " . implode(", ", $pdo->errorInfo()));
            }
            
            $result = $stmt->execute(array_values($data));
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("SQL execute failed: " . $errorInfo[2]);
            }
            
            $userId = $pdo->lastInsertId();

            // Create user directory
            $userDir = "uploads/users/$userId";
            if (!is_dir($userDir) ){
                if (!mkdir($userDir, 0755, true)) {
                    throw new Exception("Failed to create user directory: $userDir");
                }
            }

            // Send welcome email
            $subject = "Welcome to MediClinic";
            $message = "Hello $firstName,\n\nThank you for creating an account with MediClinic!\n\n";
            $message .= "Account Type: " . ucfirst($userType) . "\n";
            if ($userType === 'doctor') {
                $message .= "Specialty: $specialty\n";
            } elseif ($userType === 'hospital') {
                $message .= "Facility Name: $hospitalName\n";
            }
            $message .= "Email: $email\n\n";
            $message .= "You can now log in to your account.\n\nBest regards,\nThe MediClinic Team";
            $headers = "From: no-reply@mediclinic.com";
            mail($email, $subject, $message, $headers);

            // Log registration
            $logMessage = "[" . date('Y-m-d H:i:s') . "] New $userType registered: $email (ID: $userId)\n";
            error_log($logMessage, 3, "logs/registrations.log");

            // Clear buffer and return success
            ob_end_clean();
            echo json_encode([
                'success' => true,
                'userType' => $userType,
                'userId' => $userId
            ]);
            exit();
        } else {
            // Clear buffer and return errors
            ob_end_clean();
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }
    } catch (PDOException $e) {
        // Log and return database error with details
        $errorMessage = "Database error: " . $e->getMessage();
        error_log($errorMessage);
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'errors' => [$errorMessage] // Show actual error for debugging
        ]);
        exit();
    } catch (Exception $e) {
        // Log and return general error with details
        $errorMessage = "General error: " . $e->getMessage();
        error_log($errorMessage);
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'errors' => [$errorMessage] // Show actual error for debugging
        ]);
        exit();
    }
} else {
    // Handle invalid request method
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'errors' => ['Invalid request method.']
    ]);
    exit();
}