<?php
require_once 'config.php';

$patient_id = $_SESSION['user_id'];
$response = ['success' => false];

// Validate inputs
if (empty($_POST['test_type']) || empty($_POST['test_name'])) {
    $response['message'] = "Test type and name are required";
    echo json_encode($response);
    exit;
}

try {
    $doctor_id = !empty($_POST['doctor_id']) ? $_POST['doctor_id'] : null;
    $test_name = $_POST['test_name'] . " (" . $_POST['test_type'] . ")";
    
    $stmt = $pdo->prepare("
        INSERT INTO lab_orders 
        (patient_id, doctor_id, test_name, order_date, status) 
        VALUES (?, ?, ?, CURDATE(), 'pending')
    ");
    
    $stmt->execute([$patient_id, $doctor_id, $test_name]);
    
    $response['success'] = true;
    $response['message'] = "Test request submitted successfully";
} catch (PDOException $e) {
    $response['message'] = "Database error: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>