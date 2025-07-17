<?php
// pre_admission_form.php
session_start(); // Start the session to access messages

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';

// Clear session messages after displaying them
unset($_SESSION['errors']);
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediclinic Hospital Pre-Admission</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lc76kcrAAAAAOoBx9pqla2dV3Q7mGUidMK7FRkR"></script>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 30px;
            width: 90%;
            max-width: 900px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #555;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .messages {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .messages.error {
            background-color: #ffe0e0;
            color: #d8000c;
            border: 1px solid #d8000c;
        }

        .messages.success {
            background-color: #e6ffe6;
            color: #008000;
            border: 1px solid #008000;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
        }

        .form-group > div {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }

        input,
        select {
            width: calc(100% - 12px); /* Account for padding and border */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
        }

        .required::after {
            content: " *";
            color: red;
        }

        .checkbox-group {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .checkbox-group label {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            font-weight: normal;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 5px;
        }

        .button-group {
            text-align: right;
        }

        .button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .recaptcha-container {
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .version {
            text-align: left;
            font-size: 11px;
            color: #999;
            margin-bottom: 10px;
        }

        @media (max-width: 600px) {
            .form-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>


    <div class="container">
    <div class="logo">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                
                <h1 style="display: inline; font-size: 24px; color: #2a4365; margin-left: 10px; text-shadow: 2px 2px 5px black;">
                    TELEMEDICINE<sup><i class="fas fa-clinic-medical" style="font-size: 16px; color: #4299e1; margin-left: 5px;"></i></sup>
                </h1>
            </a>
        </div>
        <div class="header">
            
            <h1>HOSPITAL PRE-ADMISSION</h1>
            <img src="images/reg.jpg" style="width: 100%; height: 500px; max-width: 100%; margin: 50px auto;" >
            <p>Welcome to the Mediclinic online pre-admission process.<br>Please begin a new or resume an existing pre-admission, by completing the following five fields.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="messages error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="messages success">
                <p><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
        <?php endif; ?>

        <form action="process_admission.php" method="POST">
            <div class="form-group">
                <div>
                    <label for="patientSurname" class="required">PATIENT SURNAME</label>
                    <input type="text" id="patientSurname" name="patientSurname" required value="<?php echo isset($_POST['patientSurname']) && empty($successMessage) ? htmlspecialchars($_POST['patientSurname']) : ''; ?>">
                </div>
                <div>
                    <label for="hospitalName" class="required">HOSPITAL NAME</label>
                    <select id="hospitalName" name="hospitalName" required>
                        <option value="" disabled <?php echo (!isset($_POST['hospitalName']) || empty($_POST['hospitalName']) || !empty($successMessage)) ? 'selected' : ''; ?>>Select a Hospital</option>
                        <option value="Mediclinic City Hospital" <?php echo (isset($_POST['hospitalName']) && $_POST['hospitalName'] == 'Mediclinic City Hospital' && empty($successMessage)) ? 'selected' : ''; ?>>Mediclinic City Hospital</option>
                        <option value="Mediclinic Parkview Hospital" <?php echo (isset($_POST['hospitalName']) && $_POST['hospitalName'] == 'Mediclinic Parkview Hospital' && empty($successMessage)) ? 'selected' : ''; ?>>Mediclinic Parkview Hospital</option>
                        <option value="Mediclinic Welcare Hospital" <?php echo (isset($_POST['hospitalName']) && $_POST['hospitalName'] == 'Mediclinic Welcare Hospital' && empty($successMessage)) ? 'selected' : ''; ?>>Mediclinic Welcare Hospital</option>
                        <option value="Other" <?php echo (isset($_POST['hospitalName']) && $_POST['hospitalName'] == 'Other' && empty($successMessage)) ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label for="idType" class="required">ID TYPE</label>
                    <select id="idType" name="idType" required>
                        <option value="" disabled <?php echo (!isset($_POST['idType']) || empty($_POST['idType']) || !empty($successMessage)) ? 'selected' : ''; ?>>Select ID Type</option>
                        <option value="passport" <?php echo (isset($_POST['idType']) && $_POST['idType'] == 'passport' && empty($successMessage)) ? 'selected' : ''; ?>>Passport</option>
                        <option value="national_id" <?php echo (isset($_POST['idType']) && $_POST['idType'] == 'national_id' && empty($successMessage)) ? 'selected' : ''; ?>>National ID</option>
                        <option value="drivers_license" <?php echo (isset($_POST['idType']) && $_POST['idType'] == 'drivers_license' && empty($successMessage)) ? 'selected' : ''; ?>>Driver's License</option>
                    </select>
                </div>
                <div>
                    <label for="patientIdNumber" class="required">PATIENT ID NUMBER</label>
                    <input type="text" id="patientIdNumber" name="patientIdNumber" required value="<?php echo isset($_POST['patientIdNumber']) && empty($successMessage) ? htmlspecialchars($_POST['patientIdNumber']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label for="dateOfAdmission" class="required">DATE OF ADMISSION</label>
                    <input type="date" id="dateOfAdmission" name="dateOfAdmission" required value="<?php echo isset($_POST['dateOfAdmission']) && empty($successMessage) ? htmlspecialchars($_POST['dateOfAdmission']) : ''; ?>">
                </div>
                <div>
                    </div>
            </div>

            <div class="recaptcha-container">
          
                <div style="text-align: center; margin-top: 5px; font-size: 10px; color: #999;">
                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" style="color: #007bff; text-decoration: none;">Privacy</a> - <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" style="color: #007bff; text-decoration: none;">Terms</a>
                </div>
            </div>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="termsAndConditions" <?php echo (isset($_POST['termsAndConditions']) && $_POST['termsAndConditions'] == 'on' && empty($successMessage)) ? 'checked' : ''; ?>> I acknowledge that I have read and agree to the <a href="#" target="_blank">Terms and Conditions</a>
                </label>
            </div>

            <div class="button-group">
                <button type="submit" class="button">Next</button>
            </div>
        </form>

        <div class="version">version 1.4</div>

        <div class="footer">
            For website support, contact us at <a href="https://www.instagram.com/thatboytim22/">timothymorandi@gmail.com</a>
        </div>
    </div>
    <script>
  function onClick(e) {
    e.preventDefault();
    grecaptcha.enterprise.ready(async () => {
      const token = await grecaptcha.enterprise.execute('6Lc76kcrAAAAAOoBx9pqla2dV3Q7mGUidMK7FRkR', {action: 'LOGIN'});
    });
}
grecaptcha.ready(function() {
grecaptcha.execute('6Lc76kcrAAAAAOoBx9pqla2dV3Q7mGUidMK7FRkR', {action: 'submit'}).then(function(token) {
 document.querySelector('input[name="g-recaptcha-response"]').value = token;
    });
        });
          
</script>
</body>
</html>