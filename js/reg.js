// script.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Toggle password visibility in registration
    const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
    const registerPasswordInput = document.getElementById('registerPassword');

    toggleRegisterPassword.addEventListener('click', () => {
        // Toggle the type attribute
        const type = registerPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        registerPasswordInput.setAttribute('type', type);
        
        // Toggle eye icon class
        toggleRegisterPassword.classList.toggle('fa-eye-slash');
        
        // Optionally, change the title of the icon
        toggleRegisterPassword.title = type === 'password' ? 'Show Password' : 'Hide Password';
    });

   // Handle form submission
   const registrationForm = document.getElementById('registrationForm');
   registrationForm.addEventListener('submit', (event) => {
       event.preventDefault(); // Prevent default form submission

       const username = document.getElementById('registerUsername').value;
       const email = document.getElementById('registerEmail').value;
       const password = document.getElementById('registerPassword').value;

       // Here you can handle the registration logic (e.g., send a request to your server)
       console.log(`Registering with Username: ${username}, Email: ${email}, Password: ${password}`);

       // Simulate a successful registration and redirect to login page
       alert(`Registration successful! Redirecting to login...`);
       
       // Redirect to login page
       window.location.href = 'login.html'; // Change this to your actual login page URL
   });
});