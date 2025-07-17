<?php
// File: backend/get_agora_token.php
require_once __DIR__ . '/../vendor/autoload.php'; // Include Agora SDK
use Agora\AccessToken\AccessToken;
use Agora\AccessToken\RtcTokenBuilder;

session_start();

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$channelName = $data['channel'] ?? '';

// Validate input
if (empty($channelName)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Channel name required']);
    exit();
}

// Set your Agora App ID and App Certificate
$appId = "YOUR_AGORA_APP_ID";
$appCertificate = "YOUR_AGORA_APP_CERTIFICATE";
$uid = $_SESSION['user_id']; // Or generate a unique UID
$expireTimeInSeconds = 3600; // Token valid for 1 hour
$role = RtcTokenBuilder::RolePublisher;

try {
    $token = RtcTokenBuilder::buildTokenWithUid(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $role,
        $expireTimeInSeconds
    );
    
    echo json_encode([
        'success' => true,
        'token' => $token,
        'appId' => $appId,
        'channel' => $channelName,
        'uid' => $uid
    ]);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}