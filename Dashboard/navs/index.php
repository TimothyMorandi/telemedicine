<?php
// index.php - Main entry point for the Appointment Manager application

// Start session if not already started (important for potential future features like user authentication)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <!-- Tailwind CSS CDN - Ensure this is loaded before your custom CSS if you're mixing them -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>

    <?php
    // Include the application header
    // Assuming 'include' directory is relative to the current file or in the PHP include path
    include '../include/header2_dashboard.php';
    ?>

    <div class="app-container">
        <?php
        // Include the main content of the application
        include 'appointment.php';
        ?>
    </div>


    <!-- Custom JavaScript for application logic -->
    <script src="../assets/js/app.js"></script>
</body>
</html>