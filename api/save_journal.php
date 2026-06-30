<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../auth/config/db.php";

$user_id = $_SESSION['user_id'];

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if($title=="" || $content==""){
    die("All fields are required.");
}

$stmt = $conn->prepare("
INSERT INTO journals
(user_id,title,content)
VALUES(?,?,?)
");

$stmt->bind_param(
    "iss",
    $user_id,
    $title,
    $content
);

$stmt->execute();

header("Location: ../journal.php");
exit();