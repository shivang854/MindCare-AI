<?php
session_start();

require '../vendor/autoload.php';
require_once '../gemini.php';
include '../auth/config/db.php';

use Dompdf\Dompdf;

if(!isset($_SESSION['user_id'])){
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$conversation_id = $_SESSION['conversation_id'];

$stmt = $conn->prepare("
SELECT *
FROM chats
WHERE conversation_id=?
ORDER BY id ASC
");

$stmt->bind_param("i",$conversation_id);
$stmt->execute();

$result = $stmt->get_result();
$conversation = "";

while($row = $result->fetch_assoc()){

    $conversation .= "User: ".$row['user_message']."\n";
    $conversation .= "AI: ".$row['bot_reply']."\n\n";

}

$summaryResponse = askGeminiSummary($conversation);

$summary = $summaryResponse['candidates'][0]['content']['parts'][0]['text']
    ?? "Summary unavailable.";
    $stmt->execute();
$result = $stmt->get_result();

$html = "

<h1 style='text-align:center;color:#2c3e50;'>
🧠 MindCare AI Report
</h1>

<hr>

<h2>AI Session Summary</h2>

<div style='
border:1px solid #ccc;
padding:15px;
background:#f8f9fa;
margin-bottom:20px;
'>

".nl2br(htmlspecialchars($summary))."

</div>

<h2>Conversation History</h2>

<hr>

";

while($row = $result->fetch_assoc()){

    $html .= "
    <p><b>You:</b> ".$row['user_message']."</p>

    <p><b>MindCare:</b> ".$row['bot_reply']."</p>

    <p><b>Mood:</b> ".$row['mood']."</p>

    <p><b>Risk:</b> ".$row['risk_level']."</p>

    <hr>
    ";

}

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4','portrait');

$dompdf->render();

$dompdf->stream("MindCare_Report.pdf",[
    "Attachment"=>true
]);