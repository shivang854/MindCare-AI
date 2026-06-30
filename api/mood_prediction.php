<?php

session_start();

include "../auth/config/db.php";

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){

    echo json_encode([
        "prediction"=>"Please login first."
    ]);

    exit();

}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT mood,risk_level,user_message
FROM chats
WHERE user_id=?
ORDER BY id DESC
LIMIT 7
");

$stmt->bind_param("i",$user_id);

$stmt->execute();

$result = $stmt->get_result();

$moods = [];
$risk = "Low";

while($row = $result->fetch_assoc()){

    $moods[] = $row['mood'];

    if($row['risk_level']=="Critical"){

        $risk = "Critical";

    }
    elseif($row['risk_level']=="High" && $risk!="Critical"){

        $risk="High";

    }
    elseif($row['risk_level']=="Medium" && $risk=="Low"){

        $risk="Medium";

    }

}

$happy = count(array_filter($moods,function($m){

    return $m=="Happy";

}));

$sad = count(array_filter($moods,function($m){

    return $m=="Sad";

}));

$stress = count(array_filter($moods,function($m){

    return $m=="Stress";

}));


if($risk=="Critical"){

    $prediction = "⚠️ Your recent conversations indicate severe emotional distress. Please consider contacting a trusted person or a mental health professional.";

}
elseif($stress >= 3){

    $prediction = "🧠 Your recent chats suggest increased stress. Taking short breaks, sleeping well, and journaling may help.";

}
elseif($sad >= 3){

    $prediction = "💙 You have been feeling low recently. Try talking to someone you trust and continue expressing your thoughts through journaling.";

}
elseif($happy >= 3){

    $prediction = "🌱 Your emotional wellbeing looks positive. Keep maintaining your healthy habits.";

}
else{

    $prediction = "😊 Your mood appears stable. Continue checking in with yourself regularly.";

}

echo json_encode([
    "prediction"=>$prediction
]);