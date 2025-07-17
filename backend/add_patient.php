<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$first_name = mysqli_real_escape_string($con, $input['firstName']);
$last_name = mysqli_real_escape_string($con, $input['lastName']);
$email = mysqli_real_escape_string($con, $input['email']);
$phone = mysqli_real_escape_string($con, $input['phone']);
$dob = mysqli_real_escape_string($con, $input['dob']);

$query = "INSERT INTO users (first_name, last_name, email, phone, dob, role) 
          VALUES (?, ?, ?, ?, ?, 'patient')";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'sssss', $first_name, $last_name, $email, $phone, $dob);

if (mysqli_stmt_execute($stmt)) {
    $patient_id = mysqli_insert_id($con);
    echo json_encode(['success' => true, 'patient_id' => $patient_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add patient']);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>