<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$appointment_id = (int)$input['appointment_id'];

$query = "SELECT status, notes, updated_at 
          FROM appointment_status_history 
          WHERE appointment_id = ? 
          ORDER BY updated_at DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $appointment_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$history = [];

while ($row = mysqli_fetch_assoc($result)) {
    $history[] = $row;
}

echo json_encode(['success' => true, 'history' => $history]);
mysqli_stmt_close($stmt);
mysqli_close($con);
?>