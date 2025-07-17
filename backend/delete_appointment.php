<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$appointment_id = $_POST['id'];
$patient_id = $_SESSION['user_id'];

// Verify the appointment belongs to the user
$query = "DELETE FROM appointments 
          WHERE id = ? AND patient_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $patient_id);
$success = mysqli_stmt_execute($stmt);

if ($success && mysqli_affected_rows($con) > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete appointment']);
}

mysqli_close($con);
?>