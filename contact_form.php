<?php
session_start();
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['errors'], $_SESSION['success_message'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediclinic Hospital - Contact Information</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            margin: 40px 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2a4365;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .header img {
            width: 100%;
            height: auto;
            max-height: 220px;
            object-fit: cover;
            border-radius: 8px;
            margin: 20px 0;
        }

        .form-group {
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
            font-size: 15px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        .phone-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px;
            position: relative;
        }

        .phone-input-group select {
            flex: 0 0 140px;
            border: none;
            background: transparent;
        }

        .phone-input-group input {
            flex: 1;
            border: none;
            padding-left: 10px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: flex-end;
        }

        .button {
            padding: 12px 35px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .button.primary {
            background: #4299e1;
            color: white;
        }

        .button.secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .messages {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .messages.error {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .messages.success {
            background: #c6f6d5;
            color: #2f855a;
            border: 1px solid #9ae6b4;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #718096;
            font-size: 14px;
        }

        .error-message {
            color: #c53030;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
        }

        .loading-spinner {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4299e1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .phone-input-group {
                flex-direction: column;
                align-items: stretch;
                padding: 10px;
            }
            
            .phone-input-group select {
                width: 100%;
                padding: 10px;
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
            <h1>CONTACT INFORMATION</h1>
            <img src="images/reg.jpg" alt="Medical illustration">
            <p>The following information relates to the patient or parent.<br>Please note that the information below will be verified via OTP (One Time Pin) and/or verification email.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="messages error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="messages success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <form action="process_contact.php" method="POST" id="contactForm">
            <div class="form-group">
                <div>
                    <label for="mobileNumber" class="required">MOBILE NUMBER</label>
                    <div class="phone-input-group">
                        <div class="loading-spinner"></div>
                        <select id="countryCode" name="countryCode" required style="opacity: 0">
                            <option value="" disabled selected>Loading countries...</option>
                        </select>
                        <input type="tel" id="mobileNumber" name="mobileNumber" placeholder="Enter mobile number" 
                               value="<?= htmlspecialchars($formData['mobileNumber'] ?? '') ?>" required>
                    </div>
                    <div class="error-message" id="mobileNumberError">Valid mobile number required</div>
                </div>

                <div>
                    <label for="emailAddress" class="required">EMAIL ADDRESS</label>
                    <input type="email" id="emailAddress" name="emailAddress" placeholder="Enter email address"
                           value="<?= htmlspecialchars($formData['emailAddress'] ?? '') ?>" required>
                    <div class="error-message" id="emailAddressError">Valid email address required</div>
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="button secondary" onclick="window.location.href='index.php';">Cancel</button>
                <button type="submit" class="button primary">Continue</button>
            </div>
        </form>

        <div class="footer">
            <p>For support, contact <a href="mailto:timothymorandi@gmail.com">timothymorandi@gmail.com</a></p>
            <p class="version">Version 1.4</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const countryCodeSelect = document.getElementById('countryCode');
            const loadingSpinner = document.querySelector('.loading-spinner');

            async function fetchCountries() {
                try {
                    loadingSpinner.style.display = 'block';
                    const response = await fetch('https://restcountries.com/v3.1/all?fields=name,idd,cca2');
                    
                    if (!response.ok) throw new Error('API failed');
                    const countries = await response.json();

                    const sortedCountries = countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
                    
                    countryCodeSelect.innerHTML = '';
                    const defaultOption = new Option('Select Country', '');
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    countryCodeSelect.add(defaultOption);

                    sortedCountries.forEach(country => {
                        const dialCode = country.idd?.root + (country.idd?.suffixes?.[0] || '');
                        if (dialCode) {
                            const option = new Option(`${country.name.common} (${dialCode})`, dialCode);
                            countryCodeSelect.add(option);
                        }
                    });

                    countryCodeSelect.style.opacity = '1';
                    loadingSpinner.style.display = 'none';

                } catch (error) {
                    const fallbackCountries = [
                        {code: '+1', name: 'United States'},
                        {code: '+44', name: 'United Kingdom'},
                        {code: '+27', name: 'South Africa'},
                        {code: '+91', name: 'India'},
                        {code: '+86', name: 'China'},
                    ];

                    countryCodeSelect.innerHTML = '';
                    fallbackCountries.forEach(country => {
                        const option = new Option(`${country.name} (${country.code})`, country.code);
                        countryCodeSelect.add(option);
                    });

                    countryCodeSelect.style.opacity = '1';
                    loadingSpinner.style.display = 'none';
                }
            }

            form.addEventListener('submit', function(event) {
                let isValid = true;
                const mobileNumber = document.getElementById('mobileNumber').value;
                const email = document.getElementById('emailAddress').value;

                // Mobile validation
                if (!/^\d{6,15}$/.test(mobileNumber)) {
                    document.getElementById('mobileNumberError').style.display = 'block';
                    isValid = false;
                }

                // Email validation
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    document.getElementById('emailAddressError').style.display = 'block';
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });

            fetchCountries();
        });
    </script>
</body>
</html>