<?php

session_start();

header("Content-Type: application/json");

include "../auth/config/db.php";
require "../gemini.php";

if(!isset($_SESSION['user_id'])){
    echo json_encode([
        "success"=>false
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Recent Chats
$stmt = $conn->prepare("
SELECT user_message,mood,risk_level
FROM chats
WHERE user_id=?
ORDER BY id DESC
LIMIT 15
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();

$conversation = "";

while($row = $result->fetch_assoc()){

    $conversation .=
    "User: ".$row['user_message']."\n".
    "Mood: ".$row['mood']."\n".
    "Risk: ".$row['risk_level']."\n\n";

}

if($conversation==""){

    echo json_encode([
        "success"=>true,
        "insight"=>"Start chatting with MindCare AI to receive personalized wellness insights."
    ]);

    exit();

}

$prompt = "

You are an AI mental wellness assistant.

Analyze the following conversation history.

Give only one paragraph between 40 and 70 words.

Mention:

overall emotional trend

one positive observation

one helpful suggestion

Do not use bullet points.

Conversation:

".$conversation;

$response = askGemini($prompt);

$insight =
$response['candidates'][0]['content']['parts'][0]['text']
?? "No insight available.";

echo json_encode([
    "success"=>true,
    "insight"=>$insight
]);