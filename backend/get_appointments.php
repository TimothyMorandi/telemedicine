<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

$patient_id = $_SESSION['user_id'];

// FIX 1: Combine date and time for accurate comparison
$query = "SELECT a.id, 
                 a.appointment_date, 
                 a.appointment_time, 
                 a.specialty,
                 a.status,
                 CONCAT(u.first_name, ' ', u.last_name) AS doctor_name,
                 u.specialty AS doctor_specialty
          FROM appointments a
          JOIN users u ON a.doctor_id = u.id
          WHERE a.patient_id = ?
          AND (a.status = 'confirmed' OR a.status = 'pending')
          AND CONCAT(a.appointment_date, ' ', a.appointment_time) >= NOW()
          ORDER BY a.appointment_date, a.appointment_time";

$stmt = mysqli_prepare($con, $query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . mysqli_error($con)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $patient_id);
if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'message' => 'Execute failed: ' . mysqli_stmt_error($stmt)]);
    exit;
}

$result = mysqli_stmt_get_result($stmt);

$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

echo json_encode([
    'success' => true,
    'appointments' => $appointments
]);

mysqli_stmt_close($stmt);
mysqli_close($con);