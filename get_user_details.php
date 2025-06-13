<?php
use Phppot\Message;
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Accès non autorisé.']);
    exit();
}

require_once './Model/Message.php';

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID utilisateur manquant ou invalide.']);
    exit();
}

try {
    $userId = (int)$_GET['user_id'];
    $message = new Message();
    
    $details = $message->getUserVoteDetails($userId);
    $stats = $message->getUserVoteStats($userId);

    $response_data = [
        'votes' => $details,
        'stats' => $stats
    ];

    $response = ['status' => 'success', 'data' => $response_data];

} catch (Exception $e) {
    http_response_code(500);
    $response = ['status' => 'error', 'message' => 'Erreur serveur critique: ' . $e->getMessage()];
}

echo json_encode($response);
?>