<?php
session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode([
        "success" => false
    ]);
    exit();
}

include "../auth/config/db.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT trusted_name,trusted_phone,trusted_email
FROM users
WHERE id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if(
    empty($user['trusted_name']) &&
    empty($user['trusted_phone']) &&
    empty($user['trusted_email'])
){
    echo json_encode([
        "success"=>false
    ]);
    exit();
}

echo json_encode([
    "success"=>true,
    "name"=>$user['trusted_name'],
    "phone"=>$user['trusted_phone'],
    "email"=>$user['trusted_email']
]);