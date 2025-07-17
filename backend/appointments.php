<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    json_response(false, "Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new appointment
    $data = json_decode(file_get_contents('php://input'), true);
    
    $doctor_id = sanitize_input($data['doctor_id']);
    $date = sanitize_input($data['appointment_date']);
    $time = sanitize_input($data['appointment_time']);
    $reason = sanitize_input($data['appointment_reason']);
    
    $stmt = $con->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $doctor_id, $date, $time, $reason);
    
    if ($stmt->execute()) {
        json_response(true, "Appointment scheduled successfully", ['appointment_id' => $stmt->insert_id]);
    }
    json_response(false, "Failed to schedule appointment");
} else {
    // Get appointments
    if ($user_type === 'doctor') {
        $stmt = $con->prepare("SELECT a.*, u.first_name, u.last_name 
                              FROM appointments a
                              JOIN users u ON a.patient_id = u.id
                              WHERE doctor_id = ?");
    } else {
        $stmt = $con->prepare("SELECT a.*, u.first_name, u.last_name, u.specialty 
                              FROM appointments a
                              JOIN users u ON a.doctor_id = u.id
                              WHERE patient_id = ?");
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = [];
    
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
    json_response(true, "Appointments retrieved", $appointments);
}
?>