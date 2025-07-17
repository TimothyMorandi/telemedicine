// Redirect to the registration page
function redirectToRegister() {
  window.location.href = '/register';
}

// Open the modal for login type selection
function openLoginModal() {
  document.getElementById('loginModal').style.display = 'block'; // Show modal
}

// Close the login modal
function closeLoginModal() {
  document.getElementById('loginModal').style.display = 'none'; // Hide modal
}

// Handle login type selection and store it in session storage
function loginAs(type) {
  sessionStorage.setItem('loginType', type); // Store the login type
  
  // Redirect to the appropriate route based on login type
  if (type === 'admin') {
      window.location.href = '/admin_login';
  } else if (type === 'user') {
      window.location.href = '/login';
  }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
  const modal = document.getElementById('loginModal');
  if (event.target == modal) {
      closeLoginModal(); // Call the close function
  }
};