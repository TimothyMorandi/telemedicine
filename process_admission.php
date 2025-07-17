<?php
// process_admission.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Initialize variables
$errors = [];
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA verification
    $recaptchaSecret = 'your_secret_key';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    
    if (!empty($recaptchaResponse)) {
        $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $recaptchaSecret,
            'response' => $recaptchaResponse
        ];
        
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($verifyUrl, false, $context);
        $recaptchaData = json_decode($result);
        
        if (!$recaptchaData->success) {
            $errors[] = 'reCAPTCHA verification failed. Please try again.';
        }
    } else {
        $errors[] = 'Please complete the reCAPTCHA.';
    }

    // ... rest of your validation code ...

    if (empty($errors)) {
        // Use proper date formatting
        $dateOfAdmission = date('Y-m-d', strtotime($dateOfAdmission));
        
        $sql = "INSERT INTO admissions (patient_surname, hospital_name, id_type, patient_id_number, date_of_admission, terms_agreed) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", 
                $patientSurname, 
                $hospitalName, 
                $idType, 
                $patientIdNumber, 
                $dateOfAdmission, 
                $termsAgreed
            );
            
            if ($stmt->execute()) {
                $successMessage = "Pre-admission submitted successfully!";
                $_SESSION['form_data'] = [];
            } else {
                error_log("Database Error: " . $stmt->error);
                $errors[] = "Database error. Please try again later.";
            }
            $stmt->close();
        } else {
            error_log("Database Error: " . $conn->error);
            $errors[] = "Database error. Please try again later.";
        }
    }
    
    $conn->close();
    
    $_SESSION['errors'] = $errors;
    $_SESSION['success_message'] = $successMessage;
    $_SESSION['form_data'] = $_POST;  // Preserve all form data
    header("Location: contact_form.php");
    exit;
}

?>