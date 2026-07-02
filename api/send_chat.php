<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error) {
        echo json_encode($error);
    }
});

include '../auth/config/db.php';

require_once __DIR__ . '/../gemini.php';





header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit();
}

if (!isset($_SESSION['conversation_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Conversation ID missing."
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$conversation_id = $_SESSION['conversation_id'];

$message = trim($_POST['message'] ?? '');

/* ---------------- MOOD DETECTION ---------------- */

$text = strtolower($message);

$mood = "Neutral";

if (
    str_contains($text, "sad") ||
    str_contains($text, "depressed") ||
    str_contains($text, "cry") ||
    str_contains($text, "alone")
) {
    $mood = "Sad";
}

elseif (
    str_contains($text, "stress") ||
    str_contains($text, "anxiety") ||
    str_contains($text, "tension") ||
    str_contains($text, "worried")
) {
    $mood = "Stress";
}

elseif (
    str_contains($text, "happy") ||
    str_contains($text, "great") ||
    str_contains($text, "good") ||
    str_contains($text, "awesome")
) {
    $mood = "Happy";
}

elseif (
    str_contains($text, "angry") ||
    str_contains($text, "mad") ||
    str_contains($text, "hate")
) {
    $mood = "Angry";
}
/* ---------------- RISK DETECTION ---------------- */

$risk = "Low";

if (
    str_contains($text, "kill myself") ||
    str_contains($text, "suicide") ||
    str_contains($text, "want to die") ||
    str_contains($text, "end my life")
) {
    $risk = "Critical";
}

elseif (
    str_contains($text, "nobody cares") ||
    str_contains($text, "life is meaningless") ||
    str_contains($text, "hopeless")
) {
    $risk = "High";
}

elseif (
    str_contains($text, "depressed") ||
    str_contains($text, "alone") ||
    str_contains($text, "worthless")
) {
    $risk = "Medium";
}
if ($message == "") {
    echo json_encode([
        "success" => false,
        "message" => "Message cannot be empty."
    ]);
    exit();
}

/* ---------------- USER MEMORY ---------------- */

$memory = "";

$memoryStmt = $conn->prepare("
SELECT memory
FROM user_memory
WHERE user_id=?
LIMIT 1
");

$memoryStmt->bind_param("i",$user_id);

$memoryStmt->execute();

$memoryResult = $memoryStmt->get_result();

if($memoryResult->num_rows > 0){

    $memory = $memoryResult->fetch_assoc()['memory'];

}


/* ---------------- GEMINI ---------------- */
$history = "";

$historyStmt = $conn->prepare("
SELECT user_message, bot_reply
FROM chats
WHERE conversation_id=?
ORDER BY id ASC
LIMIT 10
");

$historyStmt->bind_param("i", $conversation_id);
$historyStmt->execute();

$historyResult = $historyStmt->get_result();

while($chat = $historyResult->fetch_assoc()){

    $history .= "User: ".$chat['user_message']."\n";
    $history .= "Assistant: ".$chat['bot_reply']."\n\n";

}

$fullPrompt = "";

if($memory != ""){

    $fullPrompt .= "User Memory:\n";
    $fullPrompt .= $memory;
    $fullPrompt .= "\n\n";

}

$fullPrompt .= $history;

$fullPrompt .= "User: ".$message;

$response = askGemini($fullPrompt);






if (isset($response['error'])) {

    $reply = "Gemini Error: " . $response['error']['message'];

} elseif (isset($response['candidates'][0]['content']['parts'][0]['text'])) {

    $reply = $response['candidates'][0]['content']['parts'][0]['text'];

} else {

    $reply = "Unexpected Gemini Response: " . json_encode($response);
}

/* ---------------- INSERT ---------------- */

$stmt = $conn->prepare("

INSERT INTO chats
(user_id, conversation_id, user_message, bot_reply, mood, risk_level)
VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit();
}
$titleCheck = $conn->prepare("
SELECT title
FROM conversations
WHERE id=?
");

$titleCheck->bind_param("i", $conversation_id);
$titleCheck->execute();

$currentTitle = $titleCheck->get_result()->fetch_assoc()['title'];

if($currentTitle == "New Chat"){
    $titlePrompt = "
Generate a short chat title.

Rules:
- Maximum 4 words
- No quotes
- No emojis
- No punctuation
- Only return the title

User message:
".$message;

$titleResponse = askGemini($titlePrompt);

if(isset($titleResponse['candidates'][0]['content']['parts'][0]['text'])){

    $newTitle = trim(
        $titleResponse['candidates'][0]['content']['parts'][0]['text']
    );

}else{

    $newTitle = substr($message,0,30);

}

    

    $update = $conn->prepare("
    UPDATE conversations
    SET title=?
    WHERE id=?
    ");

    $update->bind_param("si", $newTitle, $conversation_id);
    $update->execute();
}
$stmt->bind_param(
    "iissss",
    $user_id,
    $conversation_id,
    $message,
    $reply,
    $mood,
    $risk
);




if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);
    exit();
}
echo json_encode([
    "success" => true,
    "user" => $message,
    "bot" => $reply,
    "risk" => $risk
]);
exit();