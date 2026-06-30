<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'];
// ================= USER STATS =================

// Total Chats
$chatCount = $conn->prepare("
SELECT COUNT(*) as total
FROM chats
WHERE user_id=?
");

$chatCount->bind_param("i",$user_id);
$chatCount->execute();

$totalChats = $chatCount->get_result()->fetch_assoc()['total'];


// Total Journals
$journalCount = $conn->prepare("
SELECT COUNT(*) as total
FROM journals
WHERE user_id=?
");

$journalCount->bind_param("i",$user_id);
$journalCount->execute();

$totalJournals = $journalCount->get_result()->fetch_assoc()['total'];


// Most Common Mood
$moodStmt = $conn->prepare("
SELECT mood, COUNT(*) as total
FROM chats
WHERE user_id=?
AND mood!=''
GROUP BY mood
ORDER BY total DESC
LIMIT 1
");

$moodStmt->bind_param("i",$user_id);
$moodStmt->execute();

$moodData = $moodStmt->get_result()->fetch_assoc();

$topMood = $moodData['mood'] ?? "No Data";

/* =====================================================
   CREATE FIRST CONVERSATION IF USER HAS NONE
===================================================== */

$checkConversation = $conn->prepare("
SELECT id
FROM conversations
WHERE user_id=?
ORDER BY created_at DESC
LIMIT 1
");

$checkConversation->bind_param("i", $user_id);
$checkConversation->execute();

$conversationResult = $checkConversation->get_result();

if($conversationResult->num_rows == 0){

    $title = "New Chat";

    $create = $conn->prepare("
    INSERT INTO conversations(user_id,title)
    VALUES(?,?)
    ");

    $create->bind_param("is",$user_id,$title);
    $create->execute();

    $_SESSION['conversation_id'] = $conn->insert_id;

}else{

    $latestConversation = $conversationResult->fetch_assoc();

    if(!isset($_SESSION['conversation_id'])){
        $_SESSION['conversation_id'] = $latestConversation['id'];
    }

}

/* =====================================================
   OPEN CONVERSATION
===================================================== */

if(isset($_GET['conversation'])){

    $conversation_id = (int)$_GET['conversation'];

    $_SESSION['conversation_id'] = $conversation_id;

}else{

    $conversation_id = $_SESSION['conversation_id'];

}

/* =====================================================
   SECURITY CHECK
===================================================== */

$verify = $conn->prepare("
SELECT id
FROM conversations
WHERE id=? AND user_id=?
");

$verify->bind_param("ii",$conversation_id,$user_id);
$verify->execute();

if($verify->get_result()->num_rows==0){

    unset($_SESSION['conversation_id']);

    header("Location: chat.php");
    exit();

}

/* =====================================================
   LOAD CURRENT CHAT
===================================================== */

$stmt = $conn->prepare("
SELECT *
FROM chats
WHERE conversation_id=?
ORDER BY id ASC
");

$stmt->bind_param("i",$conversation_id);
$stmt->execute();

$result = $stmt->get_result();

/* =====================================================
   LOAD CHAT HISTORY
===================================================== */

$history = $conn->prepare("
SELECT id,title
FROM conversations
WHERE user_id=?
ORDER BY created_at DESC
");

$history->bind_param("i",$user_id);
$history->execute();

$historyResult = $history->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MindCare AI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

</head>


<body>

<div class="container-fluid">

<div class="row vh-100">
    


<!-- ================= SIDEBAR ================= -->

<div class="col-md-3 bg-dark text-white p-4">

    <h2 class="mb-3">
        🧠 MindCare AI
    </h2>

    <hr>
    <div class="card bg-secondary text-white mb-3">

    <div class="card-body">

        <h5>👤 User</h5>

        <small class="text-success">
            ● Online
        </small>

        <hr>

        <p>💬 Chats:
            <strong><?php echo $totalChats; ?></strong>
        </p>

        <p>📝 Journals:
            <strong><?php echo $totalJournals; ?></strong>
        </p>

        <p>😊 Mood:
            <strong><?php echo $topMood; ?></strong>
        </p>

    </div>

</div>

    <a href="api/new_chat.php"
       class="btn btn-primary w-100 mb-3">
       + New Chat
    </a>
    <input
type="text"
id="searchChat"
class="form-control mb-3"
placeholder="🔍 Search chat...">


    <p class="text-secondary">
        Chat History
    </p>

    <?php while($chat = $historyResult->fetch_assoc()){ ?>

        <?php
        $active = "";

        if($chat['id'] == $conversation_id){
            $active = "active";
        }
        ?>
        <div class="d-flex align-items-center mb-2 chat-item">

        
            <div class="d-flex align-items-center mb-2">

    <a href="chat.php?conversation=<?php echo $chat['id']; ?>"
       class="list-group-item list-group-item-action flex-grow-1 <?php echo $active; ?>">

        <?php echo htmlspecialchars($chat['title']); ?>

    </a>

    <a href="api/rename_chat.php?id=<?php echo $chat['id']; ?>"
       class="btn btn-warning btn-sm ms-2">

       ✏️

    </a>

    <a href="api/delete_chat.php?id=<?php echo $chat['id']; ?>"
       class="btn btn-danger btn-sm ms-2"
       onclick="return confirm('Delete this conversation?')">

       🗑

    </a>

</div>

          
        

        </div>

    <?php } ?>

</div>

<!-- ================= CHAT AREA ================= -->

<div class="col-md-9 d-flex flex-column">
    <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center shadow-sm">

    <div>
        <h4 class="mb-0">🧠 MindCare AI</h4>
        <small class="text-success">● Online</small>
    </div>

    <div>

        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
            📊 Dashboard
        </a>

        <a href="journal.php" class="btn btn-outline-success btn-sm">
            📝 Journal
        </a>

        <a href="profile.php" class="btn btn-outline-dark btn-sm">
            👤 Profile
        </a>

        <a href="auth/logout.php" class="btn btn-danger btn-sm">
            Logout
        </a>

    </div>

</div>


   


    <div
        id="chatWindow"
        class="flex-grow-1 overflow-auto p-4">
        <?php

if($result->num_rows == 0){

?>

<div class="text-center mt-5">

    <h2>🧠 MindCare AI</h2>

    <p class="text-muted fs-5">
        How are you feeling today?
    </p>

    <div class="mt-4">

        <button class="btn btn-outline-success m-2">😊 Happy</button>

        <button class="btn btn-outline-primary m-2">😌 Calm</button>

        <button class="btn btn-outline-warning m-2">😰 Stressed</button>

        <button class="btn btn-outline-danger m-2">😔 Sad</button>

    </div>

    <p class="text-secondary mt-4">
        Start typing below to begin your conversation.
    </p>

</div>

<?php

}else{

while($row = $result->fetch_assoc()){

?>

        

            <div class="chat-message user mb-3">

                <strong>You:</strong>

                <br>

                <?php echo htmlspecialchars($row['user_message']); ?>

            </div>

            <div class="chat-message ai mb-4">

                <strong>MindCare:</strong>

                <br>

                <?php echo nl2br(htmlspecialchars($row['bot_reply'])); ?>
                <?php
if(!empty($row['mood'])){

    $emoji = "😐";

    switch($row['mood']){

        case "Happy":
            $emoji = "😊";
            break;

        case "Sad":
            $emoji = "😔";
            break;

        case "Stress":
            $emoji = "😰";
            break;

        case "Angry":
            $emoji = "😡";
            break;
    }

    echo "<div class='mt-2 text-primary'><strong>Mood:</strong> $emoji ".$row['mood']."</div>";
}
?>



            </div>

        <?php } }?>

    </div>

<!-- Message Box -->

<div class="p-3 border-top">

<form id="chatForm">

<div class="input-group">

<textarea
id="message"
class="form-control"
rows="2"
placeholder="Type your message..."
required></textarea>

<button
type="button"
id="micBtn"
class="btn btn-outline-secondary">

🎤

</button>

<button
id="sendBtn"
class="btn btn-primary"
type="submit">

Send

</button>


</div>

</form>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const chatWindow = document.getElementById("chatWindow");
const form = document.getElementById("chatForm");
const message = document.getElementById("message");
const sendBtn = document.getElementById("sendBtn");
const micBtn = document.getElementById("micBtn");

const SpeechRecognition =
    window.SpeechRecognition || window.webkitSpeechRecognition;

let recognition = null;

if (SpeechRecognition) {

    recognition = new SpeechRecognition();

    recognition.lang = "en-US";

    recognition.continuous = false;

    recognition.interimResults = false;

}

chatWindow.scrollTop = chatWindow.scrollHeight;

message.addEventListener("keydown",function(e){

    if(e.key==="Enter" && !e.shiftKey){

        e.preventDefault();

        form.requestSubmit();

    }

});

form.addEventListener("submit",function(e){

    e.preventDefault();

    if(message.value.trim()=="") return;

    let userMessage = message.value;

    chatWindow.innerHTML += `
    <div class="chat-message user mb-3">
        <strong>You:</strong><br>
        ${userMessage}
    </div>
    `;

    chatWindow.innerHTML += `
    <div class="chat-message ai mb-4" id="typing">
        <strong>MindCare:</strong><br>
        <div class="typing">
    <span></span>
    <span></span>
    <span></span>
</div>

    </div>
    `;

    chatWindow.scrollTop = chatWindow.scrollHeight;

    sendBtn.disabled=true;
    sendBtn.innerText="Sending...";

   fetch("api/send_chat.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"message="+encodeURIComponent(userMessage)

    })
   
    
    
    .then(res => res.json())

    .then(data=>{

        document.getElementById("typing").outerHTML=`

        <div class="chat-message ai mb-4">

            <strong>MindCare:</strong><br>

            ${data.bot}
            ${(data.risk === "High" || data.risk === "Critical") ? `

<div class="alert alert-danger mt-3">

    <h5>🚨 Emergency Support</h5>

    <p class="mb-2">
        We noticed your message may indicate that you're going through a very difficult time.
    </p>
    <button
class="btn btn-success btn-sm"
onclick="stopSafetyTimer(this)">

💚 I'm Safe

</button>


    

    <a href="tel:112"
       class="btn btn-danger btn-sm ms-2">

       📞 Emergency

    </a>
    <button
class="btn btn-primary btn-sm ms-2"
onclick="notifyTrustedContact()">

👥 Notify Trusted Contact

</button>

</div>

` : ""}


        </div>

        `;
        if(data.risk==="Critical"){
    startSafetyTimer();
}
// 🔊 Speak AI Reply
const speech = new SpeechSynthesisUtterance(data.bot);

// Auto language
speech.lang = /[ऀ-ॿ]/.test(data.bot) ? "hi-IN" : "en-US";

speech.rate = 1;
speech.pitch = 1;

window.speechSynthesis.cancel();
window.speechSynthesis.speak(speech);


// 🔊 Speak AI Reply

if ("speechSynthesis" in window) {

    window.speechSynthesis.cancel();

    const speech = new SpeechSynthesisUtterance(data.bot);

    // Hindi detect
    speech.lang = /[\u0900-\u097F]/.test(data.bot)
        ? "hi-IN"
        : "en-US";

    speech.rate = 1;
    speech.pitch = 1;
    speech.volume = 1;

    window.speechSynthesis.speak(speech);

}
        message.value="";

        sendBtn.disabled=false;

        sendBtn.innerText="Send";

        chatWindow.scrollTop=chatWindow.scrollHeight;

    })

    .catch(err=>{

        console.error(err);

        document.getElementById("typing").remove();

        sendBtn.disabled=false;

        sendBtn.innerText="Send";

        alert("Something went wrong.");

    });

});


const searchChat = document.getElementById("searchChat");

searchChat.addEventListener("keyup", function () {

    const value = this.value.toLowerCase();

    const chats = document.querySelectorAll(".chat-item");

    chats.forEach(function(chat){

        if(chat.innerText.toLowerCase().includes(value)){

            chat.style.display = "flex";

        }else{

            chat.style.display = "none";

        }

    });

});
let safetyTimer = null;

function startSafetyTimer(){

    clearTimeout(safetyTimer);

    safetyTimer = setTimeout(function(){

        alert(
            "⚠️ We haven't heard from you.\n\nAre you okay?\n\nIf you need help, please contact someone you trust."
        );

    },10000); // Testing: 10 seconds
}

function stopSafetyTimer(btn){

    clearTimeout(safetyTimer);

    btn.parentElement.remove();

    alert("💚 Glad you're safe.");

}

function notifyTrustedContact(){

    fetch("api/send_email.php")

    .then(res => res.text())

    .then(data => {

        if(data.trim() == "success"){

            alert("✅ Emergency email sent successfully to your trusted contact.");

        }else{

            alert(data);

        }

    })

    .catch(err => {

        console.error(err);

        alert("Email sending failed.");

    });

}

if(recognition){

    micBtn.addEventListener("click",function(){

        recognition.start();

        micBtn.innerHTML = "🎙️";

    });

    recognition.onresult = function(event){

        message.value = event.results[0][0].transcript;

        micBtn.innerHTML = "🎤";

    };

    recognition.onerror = function(){

        micBtn.innerHTML = "🎤";

        alert("Voice recognition failed.");

    };

    recognition.onend = function(){

        micBtn.innerHTML = "🎤";

    };

}else{

    micBtn.disabled = true;

    micBtn.innerHTML = "❌";

}


</script>

</body>

</html>