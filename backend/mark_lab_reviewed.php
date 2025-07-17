<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$lab_id = (int)$input['lab_id'];
$doctor_id = (int)$input['doctor_id'];

$query = "UPDATE lab_results SET status = 'reviewed' WHERE id = ? AND doctor_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'ii', $lab_id, $doctor_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark lab result as reviewed']);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>