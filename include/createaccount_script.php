<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Elements ---
    const userTypeSelect = document.getElementById('userType');
    const formFieldsWrapper = document.getElementById('formFieldsWrapper');
    const employeeBlockMsg = document.getElementById('employeeBlockMsg');
    const createAccountBtn = document.getElementById('createAccountBtn');
    const doctorRegistrationGroup = document.getElementById('doctorRegistrationGroup');
    const nurseRegistrationGroup = document.getElementById('nurseRegistrationGroup');
    const specialtyGroup = document.getElementById('specialtyGroup');
    const hospitalGroup = document.getElementById('hospitalGroup');
    const doctorRegistrationInput = document.getElementById('doctorRegistration');
    const nurseRegistrationInput = document.getElementById('nurseRegistration');
    const specialtySelect = document.getElementById('specialty');
    const hospitalInput = document.getElementById('hospital');
    const passwordInput = document.getElementById('password');
    const retypePasswordInput = document.getElementById('retypePassword');
    const passwordMatchError = document.getElementById('passwordMatchError');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleRetypePasswordBtn = document.getElementById('toggleRetypePassword');
    const emailInput = document.getElementById('email');
    const retypeEmailInput = document.getElementById('retypeEmail');
    const emailMatchError = document.getElementById('emailMatchError');

    // Password requirement elements
    const passwordRequirements = {
        length: document.getElementById('passwordLength'),
        upper: document.getElementById('passwordUpper'),
        number: document.getElementById('passwordNumber'),
        special: document.getElementById('passwordSpecial'),
        unicode: document.getElementById('passwordUnicode')
    };

    // --- Only restrict admin/manager/employee types ---
    const restrictedTypes = ['admin', 'employee', 'manager'];
    const medicalSpecialties = [
        'Cardiology', 'Dermatology', 'Endocrinology', 'Gastroenterology',
        'Neurology', 'Oncology', 'Ophthalmology', 'Pediatrics',
        'Psychiatry', 'Radiology', 'Surgery', 'Urology', 'Other'
    ];

    // --- Password Toggle Functionality ---
    function setupPasswordToggle(button, input) {
        if (!button || !input) return;
        
        button.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Toggle eye icon
            const icon = button.querySelector('i');
            if (icon) {
                icon.className = type === 'password' ? 'far fa-eye' : 'far fa-eye-slash';
            }
        });
    }

    // --- License Validation Functions ---
    function validateLicenseNumber(input, prefix) {
        if (!input) return true;
        
        const value = input.value.trim();
        const regex = /^[A-Z]{2}-\d{6}$/;
        
        if (value && !regex.test(value)) {
            input.setCustomValidity(`Please enter a valid ${prefix} number in format: ${prefix}-123456`);
            input.classList.add('invalid-input');
            return false;
        } else {
            input.setCustomValidity('');
            input.classList.remove('invalid-input');
            return true;
        }
    }

    function handleLicenseValidation() {
        let isValid = true;
        
        if (doctorRegistrationGroup.style.display === 'flex' && doctorRegistrationInput) {
            if (!validateLicenseNumber(doctorRegistrationInput, 'MD')) {
                isValid = false;
            }
        }
        
        if (nurseRegistrationGroup.style.display === 'flex' && nurseRegistrationInput) {
            if (!validateLicenseNumber(nurseRegistrationInput, 'RN')) {
                isValid = false;
            }
        }
        
        return isValid;
    }

    // --- User Type Handling ---
    function toggleUserSpecificFields() {
        const userType = userTypeSelect.value;
        
        // Hide all groups by default
        doctorRegistrationGroup.style.display = 'none';
        nurseRegistrationGroup.style.display = 'none';
        specialtyGroup.style.display = 'none';
        hospitalGroup.style.display = 'none';
        employeeBlockMsg.style.display = 'none';
        
        // Reset required status
        if (doctorRegistrationInput) doctorRegistrationInput.required = false;
        if (nurseRegistrationInput) nurseRegistrationInput.required = false;
        if (specialtySelect) specialtySelect.required = false;
        if (hospitalInput) hospitalInput.required = false;
        
        // Clear values
        if (doctorRegistrationInput) doctorRegistrationInput.value = '';
        if (nurseRegistrationInput) nurseRegistrationInput.value = '';
        if (specialtySelect) specialtySelect.value = '';
        if (hospitalInput) hospitalInput.value = '';

        // Show relevant groups based on user type
        if (userType === 'doctor') {
            doctorRegistrationGroup.style.display = 'flex';
            specialtyGroup.style.display = 'flex';
            hospitalGroup.style.display = 'flex';
            if (doctorRegistrationInput) doctorRegistrationInput.required = true;
            if (specialtySelect) specialtySelect.required = true;
            if (hospitalInput) hospitalInput.required = true;
        } 
        else if (userType === 'nurse') {
            nurseRegistrationGroup.style.display = 'flex';
            hospitalGroup.style.display = 'flex';
            if (nurseRegistrationInput) nurseRegistrationInput.required = true;
            if (hospitalInput) hospitalInput.required = true;
        }
        
        // Handle restricted types
        if (restrictedTypes.includes(userType) && formFieldsWrapper) {
            formFieldsWrapper.style.display = 'none';
            employeeBlockMsg.textContent = "Please contact the Administrator to create the account for you and approve you.";
            employeeBlockMsg.style.display = 'block';
            if (createAccountBtn) createAccountBtn.disabled = true;
        } else if (formFieldsWrapper) {
            formFieldsWrapper.style.display = 'block';
            if (createAccountBtn) createAccountBtn.disabled = false;
        }
    }

    // --- Password Validation ---
    function updateValidationStatus(element, isValid) {
        if (!element) return;
        
        const icon = element.querySelector('i');
        if (icon) {
            icon.className = isValid ? 'fas fa-check-circle valid-icon' : 'fas fa-circle';
            element.classList.toggle('valid', isValid);
            element.classList.toggle('invalid', !isValid);
        }
    }
    
    function validatePassword() {
        const password = passwordInput ? passwordInput.value : '';
        const retypePassword = retypePasswordInput ? retypePasswordInput.value : '';
        
        // Update password requirements
        const validations = {
            length: password.length >= 8 && password.length <= 16,
            upper: /[A-Z]/.test(password) && /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
            unicode: !/\s/.test(password) && /^[\x00-\x7F]*$/.test(password)
        };
        
        // Update UI for each requirement
        Object.entries(validations).forEach(([key, isValid]) => {
            if (passwordRequirements[key]) {
                updateValidationStatus(passwordRequirements[key], isValid);
            }
        });
        
        // Handle password match
        const passwordsMatch = password === retypePassword;
        if (passwordMatchError) {
            passwordMatchError.style.display = (!passwordsMatch && retypePassword !== '') ? 'block' : 'none';
        }
        
        if (retypePasswordInput) {
            retypePasswordInput.classList.toggle('error', !passwordsMatch && retypePassword !== '');
        }
        
        return Object.values(validations).every(v => v) && passwordsMatch;
    }

    // --- Email Validation ---
    function validateEmails() {
        if (!emailInput || !retypeEmailInput) return true;
        
        if (emailInput.value !== retypeEmailInput.value && retypeEmailInput.value !== '') {
            if (emailMatchError) emailMatchError.style.display = "block";
            if (retypeEmailInput) retypeEmailInput.classList.add('invalid-input');
            return false;
        } else {
            if (emailMatchError) emailMatchError.style.display = "none";
            if (retypeEmailInput) retypeEmailInput.classList.remove('invalid-input');
            return true;
        }
    }

    // --- AJAX Email Availability Check ---
    let lastEmail = "";
    let emailTaken = false;
    let emailCheckTimeout;
    
    function checkEmailAvailability(callback) {
        if (!emailInput) {
            if (callback) callback(true);
            return;
        }
        
        const email = emailInput.value.trim();
        if (email === '' || email === lastEmail) {
            if (callback) callback(!emailTaken);
            return;
        }
        
        // Clear previous timeout
        if (emailCheckTimeout) clearTimeout(emailCheckTimeout);
        
        // Debounce email check
        emailCheckTimeout = setTimeout(() => {
            lastEmail = email;
            fetch('backend/check_email.php?email=' + encodeURIComponent(email))
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    emailTaken = data.taken;
                    if (emailTaken) {
                        showToast('Email has already been taken!', 'error');
                    }
                    if (callback) callback(!emailTaken);
                })
                .catch(error => {
                    console.error('Email check failed:', error);
                    emailTaken = false;
                    if (callback) callback(true);
                });
        }, 500);
    }

    // --- Form Validation ---
    function checkFormValidity() {
        // Skip validation if form is hidden
        if (formFieldsWrapper && formFieldsWrapper.style.display === 'none') {
            if (createAccountBtn) createAccountBtn.disabled = true;
            return;
        }
        
        // Validate required fields
        let allFieldsFilled = true;
        const requiredInputs = Array.from(document.querySelectorAll(
            '#formFieldsWrapper input[required], #formFieldsWrapper select[required]'
        ));
        
        requiredInputs.forEach(input => {
            if (input.offsetParent !== null && input.required && !input.disabled) {
                if (input.type === 'checkbox') {
                    if (!input.checked) allFieldsFilled = false;
                } else if (input.value.trim() === '') {
                    allFieldsFilled = false;
                }
            }
        });
        
        // Validate specific fields
        const isPasswordValid = passwordInput && passwordInput.value.length > 0 ? validatePassword() : false;
        const areEmailsMatching = validateEmails();
        const areLicensesValid = handleLicenseValidation();
        
        // Validate specialty for doctors
        let specialtyValid = true;
        if (userTypeSelect.value === 'doctor' && specialtySelect) {
            specialtyValid = medicalSpecialties.includes(specialtySelect.value);
            if (!specialtyValid) {
                specialtySelect.classList.add('invalid-input');
            } else {
                specialtySelect.classList.remove('invalid-input');
            }
        }
        
        // Validate hospital for doctors and nurses
        let hospitalValid = true;
        if ((userTypeSelect.value === 'doctor' || userTypeSelect.value === 'nurse') && hospitalInput) {
            hospitalValid = hospitalInput.value.trim() !== '';
            if (!hospitalValid) {
                hospitalInput.classList.add('invalid-input');
            } else {
                hospitalInput.classList.remove('invalid-input');
            }
        }
        
        // Update button state
        if (createAccountBtn) {
            if (allFieldsFilled && isPasswordValid && areEmailsMatching && 
                !emailTaken && areLicensesValid && specialtyValid && hospitalValid) {
                createAccountBtn.disabled = false;
                createAccountBtn.classList.remove('disabled-state');
            } else {
                createAccountBtn.disabled = true;
                createAccountBtn.classList.add('disabled-state');
            }
        }
    }

    // --- Toast Notifications ---
    function showToast(message, type = 'success') {
        // Remove existing toasts
        document.querySelectorAll('.custom-toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto remove after delay
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // --- Event Listeners ---
    if (userTypeSelect) {
        userTypeSelect.addEventListener('change', function() {
            toggleUserSpecificFields();
            checkFormValidity();
        });
    }
    
    if (specialtySelect) {
        specialtySelect.addEventListener('change', checkFormValidity);
    }
    
    // Initialize password toggles
    setupPasswordToggle(togglePasswordBtn, passwordInput);
    setupPasswordToggle(toggleRetypePasswordBtn, retypePasswordInput);
    
    // Input validation listeners
    [passwordInput, retypePasswordInput].forEach(input => {
        if (input) input.addEventListener('input', validatePassword);
    });
    
    [emailInput, retypeEmailInput].forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                validateEmails();
                if (input === emailInput) checkEmailAvailability(checkFormValidity);
                else checkFormValidity();
            });
        }
    });
    
    // License field validation
    [doctorRegistrationInput, nurseRegistrationInput, hospitalInput].forEach(input => {
        if (input) {
            input.addEventListener('blur', () => {
                if (input === doctorRegistrationInput) validateLicenseNumber(input, 'MD');
                else if (input === nurseRegistrationInput) validateLicenseNumber(input, 'RN');
                checkFormValidity();
            });
            input.addEventListener('input', checkFormValidity);
        }
    });

    // Add event listeners to all required inputs
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', checkFormValidity);
        input.addEventListener('change', checkFormValidity);
    });

    // --- Form Submission Handler ---
    const createAccountForm = document.getElementById('createAccountForm');
    if (createAccountForm) {
        createAccountForm.addEventListener('submit', function(e) {
            // Final validations
            const licensesValid = handleLicenseValidation();
            const specialtyValid = userTypeSelect.value !== 'doctor' || 
                                 (specialtySelect && medicalSpecialties.includes(specialtySelect.value));
            const hospitalValid = (userTypeSelect.value !== 'doctor' && userTypeSelect.value !== 'nurse') || 
                                 (hospitalInput && hospitalInput.value.trim() !== '');
            
            if (createAccountBtn && (createAccountBtn.disabled || emailTaken || !licensesValid || !specialtyValid || !hospitalValid)) {
                e.preventDefault();
                
                let alertMessage = 'Please ensure all fields are valid before submitting.';
                if (emailTaken) alertMessage = 'Email has already been taken!';
                else if (!licensesValid) alertMessage = 'Invalid license number format';
                else if (!specialtyValid) alertMessage = 'Please select a valid specialty';
                else if (!hospitalValid) alertMessage = 'Hospital/Institution is required';
                
                showToast(alertMessage, 'error');
                
                // Scroll to first error
                const firstError = document.querySelector('.invalid-input, .error-message:not([style*="none"])');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return false;
            }
        });
    }

    // --- Initialize Form State ---
    toggleUserSpecificFields();
    checkFormValidity();
});
</script>