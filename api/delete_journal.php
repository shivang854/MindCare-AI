<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../auth/config/db.php";

$user_id = $_SESSION['user_id'];

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
DELETE FROM journals
WHERE id=? AND user_id=?
");

$stmt->bind_param(
    "ii",
    $id,
    $user_id
);

$stmt->execute();

header("Location: ../journal.php");
exit();