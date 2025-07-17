<?php
// pre_admissions.php - Patient-facing page for online pre-admissions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic redirection if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize form data in session if not set
if (!isset($_SESSION['pre_admission_data'])) {
    $_SESSION['pre_admission_data'] = [
        'step' => 1,
        'status' => 'In Progress',
        'personal_info' => [],
        'admission_details' => [],
        'insurance' => [],
        'medical_history' => [],
        'consent' => []
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update current step
    $current_step = $_SESSION['pre_admission_data']['step'];
    
    // Handle step navigation
    if (isset($_POST['next_step'])) {
        // Validate current step before proceeding
        if (validateCurrentStep($current_step)) {
            $_SESSION['pre_admission_data']['step'] = min($current_step + 1, 5);
        }
    } elseif (isset($_POST['prev_step'])) {
        $_SESSION['pre_admission_data']['step'] = max($current_step - 1, 1);
    } elseif (isset($_POST['submit_form'])) {
        // Final form submission
        $_SESSION['pre_admission_data']['status'] = 'Submitted';
        $success_message = "Pre-admission form submitted successfully! You'll receive a confirmation email shortly.";
    }
    
    // Save form data
    saveFormData($current_step);
}

// Retrieve saved data
$saved_data = $_SESSION['pre_admission_data'];
$current_step = $saved_data['step'];

// Function to save form data
function saveFormData($step) {
    switch ($step) {
        case 1:
            $_SESSION['pre_admission_data']['personal_info'] = [
                'dob' => $_POST['dob'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'address' => $_POST['address'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'emergency_contact_name' => $_POST['emergency_contact_name'] ?? '',
                'emergency_contact_phone' => $_POST['emergency_contact_phone'] ?? ''
            ];
            break;
        case 2:
            $_SESSION['pre_admission_data']['admission_details'] = [
                'admission_date' => $_POST['admission_date'] ?? '',
                'admission_time' => $_POST['admission_time'] ?? '',
                'reason_for_visit' => $_POST['reason_for_visit'] ?? '',
                'preferred_hospital' => $_POST['preferred_hospital'] ?? ''
            ];
            break;
        case 3:
            $_SESSION['pre_admission_data']['insurance'] = [
                'has_insurance' => $_POST['has_insurance'] ?? '',
                'insurance_provider' => $_POST['insurance_provider'] ?? '',
                'policy_number' => $_POST['policy_number'] ?? '',
                'group_number' => $_POST['group_number'] ?? ''
            ];
            break;
        case 4:
            $_SESSION['pre_admission_data']['medical_history'] = [
                'allergies' => $_POST['allergies'] ?? '',
                'current_medications' => $_POST['current_medications'] ?? '',
                'past_medical_history' => $_POST['past_medical_history'] ?? '',
                'past_surgeries' => $_POST['past_surgeries'] ?? ''
            ];
            break;
        case 5:
            $_SESSION['pre_admission_data']['consent'] = [
                'consent_privacy' => isset($_POST['consent_privacy']) ? 1 : 0,
                'consent_treatment' => isset($_POST['consent_treatment']) ? 1 : 0,
                'consent_financial' => isset($_POST['consent_financial']) ? 1 : 0
            ];
            break;
    }
}

// Function to validate current step
function validateCurrentStep($step) {
    switch ($step) {
        case 1:
            return !empty($_POST['dob']) && !empty($_POST['gender']) && !empty($_POST['address']);
        case 2:
            return !empty($_POST['admission_date']) && !empty($_POST['admission_time']) && !empty($_POST['reason_for_visit']);
        case 3:
            if ($_POST['has_insurance'] === 'yes') {
                return !empty($_POST['insurance_provider']) && !empty($_POST['policy_number']);
            }
            return true;
        case 4:
            // All fields are optional in medical history
            return true;
        case 5:
            return isset($_POST['consent_privacy']) && isset($_POST['consent_treatment']) && isset($_POST['consent_financial']);
        default:
            return true;
    }
}
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Pre-Admissions - HealthConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            color: #2c3e50;
            line-height: 1.6;
        }

        .app-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #dfe6e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2a6bc9;
            box-shadow: 0 0 0 3px rgba(42, 107, 201, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .btn-primary {
            background: #2a6bc9;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1e5aaf;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #2a6bc9;
            color: #2a6bc9;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #e3edfc;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2rem;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e0e0e0;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 20%;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
            color: #fff;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: #2a6bc9;
        }

        .step.completed .step-number {
            background: #27ae60;
        }

        .step-label {
            font-size: 0.85rem;
            color: #7f8c8d;
        }

        .step.active .step-label {
            color: #2a6bc9;
            font-weight: 500;
        }

        .step.completed .step-label {
            color: #27ae60;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-in-progress {
            background-color: #e3edfc;
            color: #2a6bc9;
        }

        .status-submitted {
            background-color: #e8f5e9;
            color: #27ae60;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #27ae60;
        }
        
        .confirmation-card {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 2rem 0;
        }
        
        .confirmation-icon {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 1.5rem;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        
        .summary-table th, .summary-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dfe6e9;
        }
        
        .summary-table th {
            background-color: #f8f9fa;
            font-weight: 500;
            color: #7f8c8d;
            width: 30%;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-blue-600 text-white p-2 rounded-lg mr-3">
                    <i class="fas fa-hospital text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-blue-800">Health<span class="text-blue-600">Connect</span></h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="font-medium"><?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?> <?= htmlspecialchars($_SESSION['last_name'] ?? '') ?></p>
                    <p class="text-sm text-gray-600">Status: 
                        <span class="status-badge status-<?= str_replace(' ', '-', strtolower($saved_data['status'])) ?>">
                            <?= $saved_data['status'] ?>
                        </span>
                    </p>
                </div>
                <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center text-blue-800 font-bold">
                    <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)) ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Progress Steps -->
    <div class="container mx-auto px-4 py-6">
        <div class="step-indicator">
            <div class="step <?= $current_step >= 1 ? 'completed' : '' ?> <?= $current_step == 1 ? 'active' : '' ?>">
                <div class="step-number">1</div>
                <div class="step-label">Personal Info</div>
            </div>
            <div class="step <?= $current_step >= 2 ? 'completed' : '' ?> <?= $current_step == 2 ? 'active' : '' ?>">
                <div class="step-number">2</div>
                <div class="step-label">Admission</div>
            </div>
            <div class="step <?= $current_step >= 3 ? 'completed' : '' ?> <?= $current_step == 3 ? 'active' : '' ?>">
                <div class="step-number">3</div>
                <div class="step-label">Insurance</div>
            </div>
            <div class="step <?= $current_step >= 4 ? 'completed' : '' ?> <?= $current_step == 4 ? 'active' : '' ?>">
                <div class="step-number">4</div>
                <div class="step-label">Medical</div>
            </div>
            <div class="step <?= $current_step >= 5 ? 'completed' : '' ?> <?= $current_step == 5 ? 'active' : '' ?>">
                <div class="step-number">5</div>
                <div class="step-label">Consent</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <span><?= $success_message ?></span>
            </div>
        <?php endif; ?>
        
        <div class="card overflow-hidden">
            <div class="bg-blue-600 text-white py-4 px-6">
                <h1 class="text-2xl font-bold">Online Pre-Admissions Form</h1>
                <p class="text-blue-100 mt-1">
                    Complete this form to pre-register for your hospital visit and expedite your admission process.
                </p>
            </div>
            
            <form id="preAdmissionForm" method="POST" class="p-6">
                <!-- Step 1: Personal Information -->
                <div class="form-step <?= $current_step == 1 ? 'active' : '' ?>" id="step1">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 text-blue-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="text-xl font-bold text-blue-800">Personal Information</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="first_name" class="form-control" required
                                   value="<?= htmlspecialchars($_SESSION['first_name'] ?? '') ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="last_name" class="form-control" required
                                   value="<?= htmlspecialchars($_SESSION['last_name'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['personal_info']['dob'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?= ($saved_data['personal_info']['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($saved_data['personal_info']['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($saved_data['personal_info']['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" required
                               value="<?= htmlspecialchars($saved_data['personal_info']['address'] ?? '') ?>">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['personal_info']['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                   value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="emergencyContactName">Emergency Contact Name</label>
                            <input type="text" id="emergencyContactName" name="emergency_contact_name" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['personal_info']['emergency_contact_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="emergencyContactPhone">Emergency Contact Phone</label>
                            <input type="tel" id="emergencyContactPhone" name="emergency_contact_phone" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['personal_info']['emergency_contact_phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="flex justify-between pt-8">
                        <button type="button" class="btn-outline" onclick="location.href='dashboard.php'">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </button>
                        <button type="submit" name="next_step" class="btn-primary">
                            Next: Admission Details <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Admission Details -->
                <div class="form-step <?= $current_step == 2 ? 'active' : '' ?>" id="step2">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 text-blue-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h2 class="text-xl font-bold text-blue-800">Admission Details</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="admissionDate">Preferred Admission Date</label>
                            <input type="date" id="admissionDate" name="admission_date" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['admission_details']['admission_date'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="admissionTime">Preferred Admission Time</label>
                            <input type="time" id="admissionTime" name="admission_time" class="form-control" required
                                   value="<?= htmlspecialchars($saved_data['admission_details']['admission_time'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="reasonForVisit">Reason for Visit / Admission</label>
                        <textarea id="reasonForVisit" name="reason_for_visit" rows="3" class="form-control" required
                                  placeholder="e.g., Scheduled surgery, diagnostic tests, specialist consultation"><?= htmlspecialchars($saved_data['admission_details']['reason_for_visit'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="preferredHospital">Preferred Hospital</label>
                        <select id="preferredHospital" name="preferred_hospital" class="form-control" required>
                            <option value="">Select Hospital</option>
                            <option value="City Medical Center" <?= ($saved_data['admission_details']['preferred_hospital'] ?? '') === 'City Medical Center' ? 'selected' : '' ?>>City Medical Center</option>
                            <option value="Metro General Hospital" <?= ($saved_data['admission_details']['preferred_hospital'] ?? '') === 'Metro General Hospital' ? 'selected' : '' ?>>Metro General Hospital</option>
                            <option value="University Hospital" <?= ($saved_data['admission_details']['preferred_hospital'] ?? '') === 'University Hospital' ? 'selected' : '' ?>>University Hospital</option>
                            <option value="Children's Hospital" <?= ($saved_data['admission_details']['preferred_hospital'] ?? '') === 'Children\'s Hospital' ? 'selected' : '' ?>>Children's Hospital</option>
                            <option value="Community Health Center" <?= ($saved_data['admission_details']['preferred_hospital'] ?? '') === 'Community Health Center' ? 'selected' : '' ?>>Community Health Center</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-between pt-8">
                        <button type="submit" name="prev_step" class="btn-outline">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="submit" name="next_step" class="btn-primary">
                            Next: Insurance Information <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Insurance Information -->
                <div class="form-step <?= $current_step == 3 ? 'active' : '' ?>" id="step3">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 text-blue-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h2 class="text-xl font-bold text-blue-800">Insurance Information</h2>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="hasInsurance">Do you have medical insurance?</label>
                        <select id="hasInsurance" name="has_insurance" class="form-control" required>
                            <option value="yes" <?= ($saved_data['insurance']['has_insurance'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                            <option value="no" <?= ($saved_data['insurance']['has_insurance'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    
                    <div id="insuranceDetails" class="space-y-6">
                        <div class="form-group">
                            <label for="insuranceProvider">Insurance Provider</label>
                            <input type="text" id="insuranceProvider" name="insurance_provider" class="form-control"
                                   value="<?= htmlspecialchars($saved_data['insurance']['insurance_provider'] ?? '') ?>">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="policyNumber">Policy Number</label>
                                <input type="text" id="policyNumber" name="policy_number" class="form-control"
                                       value="<?= htmlspecialchars($saved_data['insurance']['policy_number'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="groupNumber">Group Number (Optional)</label>
                                <input type="text" id="groupNumber" name="group_number" class="form-control"
                                       value="<?= htmlspecialchars($saved_data['insurance']['group_number'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between pt-8">
                        <button type="submit" name="prev_step" class="btn-outline">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="submit" name="next_step" class="btn-primary">
                            Next: Medical History <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 4: Medical History -->
                <div class="form-step <?= $current_step == 4 ? 'active' : '' ?>" id="step4">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 text-blue-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h2 class="text-xl font-bold text-blue-800">Medical History (Brief)</h2>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="allergies">Allergies (e.g., medications, food, latex)</label>
                        <textarea id="allergies" name="allergies" rows="2" class="form-control"><?= htmlspecialchars($saved_data['medical_history']['allergies'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="currentMedications">Current Medications (with dosage and frequency)</label>
                        <textarea id="currentMedications" name="current_medications" rows="3" class="form-control"><?= htmlspecialchars($saved_data['medical_history']['current_medications'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="pastMedicalHistory">Past Medical History / Existing Conditions</label>
                        <textarea id="pastMedicalHistory" name="past_medical_history" rows="3" class="form-control"><?= htmlspecialchars($saved_data['medical_history']['past_medical_history'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="pastSurgeries">Past Surgeries (Year and Type)</label>
                        <textarea id="pastSurgeries" name="past_surgeries" rows="2" class="form-control"><?= htmlspecialchars($saved_data['medical_history']['past_surgeries'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="flex justify-between pt-8">
                        <button type="submit" name="prev_step" class="btn-outline">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </button>
                        <button type="submit" name="next_step" class="btn-primary">
                            Next: Consent <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 5: Consent -->
                <div class="form-step <?= $current_step == 5 ? 'active' : '' ?>" id="step5">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 text-blue-800 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <h2 class="text-xl font-bold text-blue-800">Consent</h2>
                    </div>
                    
                    <div class="form-group flex items-start mb-4">
                        <input type="checkbox" id="consentPrivacy" name="consent_privacy" class="h-5 w-5 text-blue-600 rounded mt-1 mr-2" required
                               <?= ($saved_data['consent']['consent_privacy'] ?? 0) ? 'checked' : '' ?>>
                        <label for="consentPrivacy" class="text-gray-700">I have read and agree to the <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>.</label>
                    </div>
                    
                    <div class="form-group flex items-start mb-4">
                        <input type="checkbox" id="consentTreatment" name="consent_treatment" class="h-5 w-5 text-blue-600 rounded mt-1 mr-2" required
                               <?= ($saved_data['consent']['consent_treatment'] ?? 0) ? 'checked' : '' ?>>
                        <label for="consentTreatment" class="text-gray-700">I consent to medical treatment as deemed necessary by the healthcare providers.</label>
                    </div>
                    
                    <div class="form-group flex items-start mb-6">
                        <input type="checkbox" id="consentFinancial" name="consent_financial" class="h-5 w-5 text-blue-600 rounded mt-1 mr-2" required
                               <?= ($saved_data['consent']['consent_financial'] ?? 0) ? 'checked' : '' ?>>
                        <label for="consentFinancial" class="text-gray-700">I understand my financial responsibility for services rendered.</label>
                    </div>
                    
                    <!-- Confirmation Section -->
                    <div class="confirmation-card">
                        <div class="confirmation-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Confirm Your Pre-Admission</h3>
                        <p class="text-gray-600 mb-6">
                            Please review your information below. Once submitted, you'll receive a confirmation email 
                            with your admission details and next steps.
                        </p>
                        
                        <table class="summary-table">
                            <tr>
                                <th>Admission Date</th>
                                <td><?= htmlspecialchars($saved_data['admission_details']['admission_date'] ?? 'Not provided') ?></td>
                            </tr>
                            <tr>
                                <th>Admission Time</th>
                                <td><?= htmlspecialchars($saved_data['admission_details']['admission_time'] ?? 'Not provided') ?></td>
                            </tr>
                            <tr>
                                <th>Preferred Hospital</th>
                                <td><?= htmlspecialchars($saved_data['admission_details']['preferred_hospital'] ?? 'Not provided') ?></td>
                            </tr>
                            <tr>
                                <th>Reason for Visit</th>
                                <td><?= htmlspecialchars($saved_data['admission_details']['reason_for_visit'] ?? 'Not provided') ?></td>
                            </tr>
                        </table>
                        
                        <div class="flex justify-center space-x-4">
                            <button type="submit" name="prev_step" class="btn-outline">
                                <i class="fas fa-edit mr-2"></i> Edit Information
                            </button>
                            <button type="submit" name="submit_form" class="btn-primary">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Pre-Admission
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="bg-blue-50 rounded-lg p-6 mt-8 flex flex-col md:flex-row items-center">
            <div class="bg-blue-100 text-blue-800 p-3 rounded-lg mb-4 md:mb-0 md:mr-6">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-blue-800 mb-2">Need Assistance?</h3>
                <p class="text-blue-700">
                    Our admissions team is available to help you complete this form. 
                    Call us at <span class="font-semibold">(555) 123-4567</span> or email 
                    <span class="font-semibold">admissions@healthconnect.example</span>.
                </p>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">HealthConnect</h3>
                    <p class="text-gray-400">
                        Providing compassionate care and innovative medical solutions for healthier communities.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Find a Doctor</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Patient Portal</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>123 Medical Center Drive<br>Healthville, HV 12345</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            <span>(555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@healthconnect.example</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Patient Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Billing & Insurance</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Medical Records</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Patient Education</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Support Groups</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> HealthConnect Medical Center. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide/show insurance details based on selection
            const hasInsuranceSelect = document.getElementById('hasInsurance');
            const insuranceDetailsDiv = document.getElementById('insuranceDetails');
            
            function toggleInsuranceDetails() {
                if (hasInsuranceSelect.value === 'yes') {
                    insuranceDetailsDiv.style.display = 'block';
                    // Make fields required
                    document.getElementById('insuranceProvider').setAttribute('required', 'required');
                    document.getElementById('policyNumber').setAttribute('required', 'required');
                } else {
                    insuranceDetailsDiv.style.display = 'none';
                    // Remove required attribute
                    document.getElementById('insuranceProvider').removeAttribute('required');
                    document.getElementById('policyNumber').removeAttribute('required');
                }
            }
            
            // Set initial state
            toggleInsuranceDetails();
            
            // Add change event listener
            hasInsuranceSelect.addEventListener('change', toggleInsuranceDetails);
            
            // Set minimum date for admissionDate to today
            const admissionDateInput = document.getElementById('admissionDate');
            const today = new Date().toISOString().split('T')[0];
            if (admissionDateInput) admissionDateInput.min = today;
            
            // Set max date for birth date to today
            const dobInput = document.getElementById('dob');
            if (dobInput) dobInput.max = today;
        });
    </script>
</body>
</html>