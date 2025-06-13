<?php
use Phppot\Message;
session_start();

// Toujours définir le type de contenu au début
header('Content-Type: application/json');

// Sécurité : Vérifier si l'utilisateur est un admin connecté
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] != 1) {
    http_response_code(403); // Interdit
    echo json_encode(['status' => 'error', 'message' => 'Accès non autorisé.']);
    exit();
}

require_once './Model/Message.php';

// Vérifier si l'ID est bien passé en POST
if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['status' => 'error', 'message' => 'ID utilisateur manquant ou invalide.']);
    exit();
}

try {
    $userId = (int)$_POST['user_id'];
    $message = new Message();
    $response = $message->deleteUserAndRevertVotes($userId);
} catch (Exception $e) {
    // Capturer les erreurs inattendues
    http_response_code(500); // Erreur Interne du Serveur
    $response = ['status' => 'error', 'message' => 'Erreur serveur critique: ' . $e->getMessage()];
}

echo json_encode($response);
?>