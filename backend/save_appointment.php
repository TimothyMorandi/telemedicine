<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Database connection
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Get input data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Add debugging output
error_log("Received data: " . print_r($rawData, true));
error_log("Decoded data: " . print_r($data, true));

// Process and validate fields
$doctor_id = isset($data['doctor']) ? (int)$data['doctor'] : 0;
$appointment_date = isset($data['appointmentDate']) ? trim($data['appointmentDate']) : '';
$appointment_time = isset($data['appointmentTime']) ? trim($data['appointmentTime']) : '';
$reason = isset($data['appointmentReason']) ? trim($data['appointmentReason']) : '';

// Single validation block
if ($doctor_id <= 0 || empty($appointment_date) || empty($appointment_time) || empty($reason)) {
    // Detailed error message for debugging
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required',
        'debug' => [
            'doctor_id' => $doctor_id,
            'appointment_date' => $appointment_date,
            'appointment_time' => $appointment_time,
            'reason' => $reason,
            'all_data' => $data
        ]
    ]);
    exit;
}

// Insert into database
$patient_id = $_SESSION['user_id'];
$status = 'pending';

$query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status) 
          VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . mysqli_error($con)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "iiissi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason, $status);

if (mysqli_stmt_execute($stmt)) {
    $appointment_id = mysqli_insert_id($con);
    echo json_encode([
        'success' => true,
        'message' => 'Appointment scheduled successfully!',
        'data' => [
            'appointment_id' => $appointment_id,
            'date' => $appointment_date,
            'time' => $appointment_time
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Execution failed: ' . mysqli_error($con)]);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
