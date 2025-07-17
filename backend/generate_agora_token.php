<?php
header('Content-Type: application/json');
require 'vendor/autoload.php'; // Include Agora PHP SDK

use Agora\RTC\TokenBuilder;

$input = json_decode(file_get_contents('php://input'), true);
$appointment_id = (int)$input['appointment_id'];
$user_id = (int)$input['user_id'];
$role = $input['role'] === 'publisher' ? 1 : 2;

$app_id = 'YOUR_AGORA_APP_ID';
$app_certificate = 'YOUR_AGORA_APP_CERTIFICATE';
$channel = 'appointment_' . $appointment_id;
$uid = $user_id;
$expire_time = 3600; // 1 hour

$token = TokenBuilder::buildTokenWithUid(
    $app_id,
    $app_certificate,
    $channel,
    $uid,
    $role,
    $expire_time
);

echo json_encode([
    'success' => true,
    'token' => $token,
    'channel' => $channel
]);
?>