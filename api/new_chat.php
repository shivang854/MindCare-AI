<?php
session_start();

include '../auth/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$title = "New Chat";

$stmt = $conn->prepare("INSERT INTO conversations (user_id, title) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $title);
$stmt->execute();

$_SESSION['conversation_id'] = $conn->insert_id;

header("Location: ../chat.php?conversation=" . $_SESSION['conversation_id']);
exit();
