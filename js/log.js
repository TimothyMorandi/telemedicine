// script.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Toggle password visibility
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');
    const loginPasswordInput = document.getElementById('loginPassword');

    toggleLoginPassword.addEventListener('click', () => {
        // Toggle the type attribute
        const type = loginPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        loginPasswordInput.setAttribute('type', type);
        
        // Toggle eye icon class
        toggleLoginPassword.classList.toggle('fa-eye-slash');
        
        // Optionally, you can change the title of the icon
        toggleLoginPassword.title = type === 'password' ? 'Show Password' : 'Hide Password';
    });

    
   // Handle form submission
   const loginForm = document.getElementById('loginForm');
   loginForm.addEventListener('submit', (event) => {
       event.preventDefault(); // Prevent default form submission

       const username = document.getElementById('loginUsername').value;
       const password = document.getElementById('loginPassword').value;

       // Here you can handle the login logic (e.g., send a request to your server)
       console.log(`Username: ${username}, Password: ${password}`);

       // You can also send an AJAX request here if needed
       // Example:
       /*
       fetch('/api/login', {
           method: 'POST',
           headers: { 'Content-Type': 'application/json' },
           body: JSON.stringify({ username, password })
       })
       .then(response => response.json())
       .then(data => console.log(data))
       .catch(error => console.error('Error:', error));
       */

       // For demonstration purposes, let's just alert the user
       alert(`Logging in with Username: ${username}`);
   });
});