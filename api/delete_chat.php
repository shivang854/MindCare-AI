
<?php
session_start();

include '../auth/config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Conversation ID missing.");
}

$user_id = $_SESSION['user_id'];
$conversation_id = (int)$_GET['id'];

// Verify ownership
$check = $conn->prepare("
SELECT id
FROM conversations
WHERE id=? AND user_id=?
");

$check->bind_param("ii",$conversation_id,$user_id);
$check->execute();

if($check->get_result()->num_rows==0){
    die("Conversation not found.");
}

// Delete all chats first
$stmt = $conn->prepare("
DELETE FROM chats
WHERE conversation_id=?
");
$stmt->bind_param("i",$conversation_id);
$stmt->execute();

// Delete conversation
$stmt = $conn->prepare("
DELETE FROM conversations
WHERE id=?
");
$stmt->bind_param("i",$conversation_id);
$stmt->execute();

// Find another conversation
$next = $conn->prepare("
SELECT id
FROM conversations
WHERE user_id=?
ORDER BY created_at DESC
LIMIT 1
");
$next->bind_param("i",$user_id);
$next->execute();

$row = $next->get_result()->fetch_assoc();

if($row){
    $_SESSION['conversation_id'] = $row['id'];
}

header("Location: ../chat.php");
exit();