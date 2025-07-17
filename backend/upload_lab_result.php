<?php
header('Content-Type: application/json');
$con = mysqli_connect("localhost", "root", "", "telemedicine");
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$patient_id = (int)$_POST['patient_id'];
$test_type = mysqli_real_escape_string($con, $_POST['test_type']);
$test_date = mysqli_real_escape_string($con, $_POST['test_date']);
$interpretation = mysqli_real_escape_string($con, $_POST['interpretation']);
$doctor_id = (int)$_POST['doctor_id'];

$result_file = '';
if (isset($_FILES['result_file']) && $_FILES['result_file']['error'] == 0) {
    $upload_dir = 'uploads/lab_results/';
    $file_name = time() . '_' . basename($_FILES['result_file']['name']);
    $target_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['result_file']['tmp_name'], $target_path)) {
        $result_file = $target_path;
    }
}

$query = "INSERT INTO lab_results (patient_id, doctor_id, test_type, test_date, result_file, result_details, status)
          VALUES (?, ?, ?, ?, ?, ?, 'pending')";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'iissss', $patient_id, $doctor_id, $test_type, $test_date, $result_file, $interpretation);

if (mysqli_stmt_execute($stmt)) {
    $lab_id = mysqli_insert_id($con);
    echo json_encode(['success' => true, 'lab_id' => $lab_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload lab result']);
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>