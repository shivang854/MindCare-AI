<?php

session_start();

header("Content-Type: application/json");

include "../auth/config/db.php";

if(!isset($_SESSION['user_id'])){

    echo json_encode([
        "success"=>false
    ]);

    exit();

}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT mood, COUNT(*) AS total
FROM chats
WHERE user_id=?
AND mood!=''
GROUP BY mood
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();

$labels = [];
$data = [];

while($row = $result->fetch_assoc()){

    $labels[] = $row['mood'];
    $data[] = (int)$row['total'];

}

echo json_encode([
    "success"=>true,
    "labels"=>$labels,
    "data"=>$data
]);