<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../auth/config/db.php";

$user_id = $_SESSION['user_id'];

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if($title=="" || $content==""){
    die("All fields are required.");
}

$stmt = $conn->prepare("
UPDATE journals
SET title=?, content=?
WHERE id=? AND user_id=?
");

$stmt->bind_param(
    "ssii",
    $title,
    $content,
    $id,
    $user_id
);

$stmt->execute();

header("Location: ../journal.php");
exit();