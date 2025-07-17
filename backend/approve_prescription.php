<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$prescription_id = (int)$input['prescription_id'];
$doctor_id = (int)$input['doctor_id'];

$query = "UPDATE prescriptions SET status = 'approved' WHERE id = ? AND doctor_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'ii', $prescription_id, $doctor_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to approve prescription']);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>