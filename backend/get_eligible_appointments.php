<?php
// File: backend/get_eligible_appointments.php
session_start();

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Database connection
$con = mysqli_connect("localhost", "root", "", "telemedicine");

// Get user ID
$userId = $_SESSION['user_id'];

// Query to get eligible appointments
$query = "SELECT 
            a.id, 
            a.appointment_date, 
            a.status,
            d.id AS doctor_id,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name,
            d.specialty
          FROM appointments a
          JOIN users d ON a.doctor_id = d.id
          WHERE a.patient_id = $userId
          AND a.appointment_date BETWEEN NOW() - INTERVAL 1 HOUR AND NOW() + INTERVAL 1 WEEK
          ORDER BY a.appointment_date ASC";

$result = mysqli_query($con, $query);

if ($result) {
    $appointments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'appointments' => $appointments
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch appointments'
    ]);
}