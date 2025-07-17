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

$query = "SELECT COUNT(*) AS count 
          FROM appointments 
          WHERE patient_id = ? 
          AND (status = 'confirmed' OR status = 'pending')
          AND CONCAT(appointment_date, ' ', appointment_time) >= NOW()";

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
$row = mysqli_fetch_assoc($result);
$count = $row['count'];

echo json_encode([
    'success' => true,
    'count' => $count,
    'message' => 'Appointment count fetched successfully',
    'patient_id' => $patient_id
]);





mysqli_stmt_close($stmt);
mysqli_close($con);