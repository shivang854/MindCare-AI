<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../auth/config/db.php";

$user_id = $_SESSION['user_id'];

$name = trim($_POST['trusted_name']);
if($name != "" && !preg_match("/^[a-zA-Z ]+$/",$name)){
    die("Invalid name.");
}
$phone = trim($_POST['trusted_phone']);
if($phone != "" && !preg_match("/^[0-9]{10}$/",$phone)){
    die("Invalid phone number.");
}
$email = trim($_POST['trusted_email']);
if($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL)){
    die("Invalid email address.");
}
$stmt = $conn->prepare("
UPDATE users
SET
trusted_name=?,
trusted_phone=?,
trusted_email=?
WHERE id=?
");

$stmt->bind_param(
    "sssi",
    $name,
    $phone,
    $email,
    $user_id
);

if($stmt->execute()){

    header("Location: ../profile.php?saved=1");
    exit();

}else{

    die("Database error.");

}
exit();