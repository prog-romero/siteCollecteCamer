<?php
use Phppot\Message;
require_once './Model/Message.php';

header('Content-Type: application/json');

$message = new Message();
$voters = $message->getVotersWithVoteCount();

echo json_encode($voters);
?>