<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$labId = isset($data['labId']) ? (int)$data['labId'] : 0;
$status = isset($data['status']) ? $data['status'] : '';

if (!$labId || !in_array($status, ['pending', 'reviewed'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

$query = "UPDATE lab_results SET status = ? WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "si", $status, $labId);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
}

mysqli_close($con);
?>