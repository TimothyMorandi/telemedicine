<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$appointment_id = (int)$input['appointment_id'];
$doctor_id = (int)$input['doctor_id'];
$status = mysqli_real_escape_string($con, $input['status']);
$notes = mysqli_real_escape_string($con, $input['notes']);

$query = "UPDATE appointments 
          SET status = ?, notes = ?
          WHERE id = ? AND doctor_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'ssii', $status, $notes, $appointment_id, $doctor_id);

if (mysqli_stmt_execute($stmt)) {
    // Log status change
    $history_query = "INSERT INTO appointment_status_history (appointment_id, status, notes, updated_at)
                      VALUES (?, ?, ?, NOW())";
    $history_stmt = mysqli_prepare($con, $history_query);
    mysqli_stmt_bind_param($history_stmt, 'iss', $appointment_id, $status, $notes);
    mysqli_stmt_execute($history_stmt);
    mysqli_stmt_close($history_stmt);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to approve appointment']);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>