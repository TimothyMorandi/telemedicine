<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$doctor_id = (int)$input['doctor_id'];
$statuses = isset($input['status']) ? $input['status'] : ['pending', 'approved'];

$placeholders = implode(',', array_fill(0, count($statuses), '?'));
$today = date('Y-m-d');
$query = "SELECT a.*, 
          p.first_name AS patient_first_name, 
          p.last_name AS patient_last_name,
          p.email AS patient_email
          FROM appointments a
          JOIN users p ON a.patient_id = p.id
          WHERE a.doctor_id = ? 
          AND a.appointment_date = ?
          AND a.status IN ($placeholders)
          ORDER BY a.appointment_time ASC";

$stmt = mysqli_prepare($con, $query);
$types = 'is' . str_repeat('s', count($statuses));
$params = array_merge([$doctor_id, $today], $statuses);
mysqli_stmt_bind_param($stmt, $types, ...$params);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$appointments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

echo json_encode(['success' => true, 'appointments' => $appointments]);
mysqli_stmt_close($stmt);
mysqli_close($con);
?>