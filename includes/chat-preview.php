
<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include 'auth/config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM chats WHERE user_id=? ORDER BY id DESC LIMIT 5");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<section class="chat-preview py-5">

<div class="container">

<div class="row align-items-center">

<!-- Left Side -->

<div class="col-lg-6">

<div class="hero-badge">
🤖 AI Therapist
</div>

<h2 class="display-5 fw-bold mt-4">
Talk Anytime with Your Personal AI
</h2>

<p class="lead mt-4">
Your AI companion is available 24×7 to help you reduce stress,
improve mood and guide your mental wellness journey.
</p>

<ul class="chat-list">
<li>✅ 24×7 Available</li>
<li>✅ Secure Conversations</li>
<li>✅ Instant AI Responses</li>
</ul>

</div>

<!-- Right Side -->

<div class="col-lg-6">

<div class="glass-card card-chat">

<div class="chat-window" id="chatWindow">

<?php while($row = $result->fetch_assoc()){ ?>

<div class="chat-message user">
<?php echo htmlspecialchars($row['user_message']); ?>
</div>

<div class="chat-message ai">
🤖 <?php echo nl2br(htmlspecialchars($row['bot_reply'])); ?>
</div>

<?php } ?>

</div>

<form id="chatForm">

<div class="mt-3">

<textarea
id="message"
class="form-control"
rows="3"
placeholder="Type your message..."
required></textarea>

</div>

<button
type="submit"
class="btn btn-primary w-100 mt-3"
id="sendBtn">

Send Message

</button>

</form>

</div>

</div>

</div>

</div>

</section>

<script>

const form = document.getElementById("chatForm");
const chatWindow = document.getElementById("chatWindow");

form.addEventListener("submit", async function(e){

    e.preventDefault();

    const messageBox = document.getElementById("message");

    const message = messageBox.value.trim();

    if(message==="") return;

    document.getElementById("sendBtn").disabled = true;

    const response = await fetch("api/send_chat.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"message="+encodeURIComponent(message)

    });

    const data = await response.json();

    document.getElementById("sendBtn").disabled = false;
    const typing = `
<div class="chat-message ai typing-message">
🤖 MindCare is typing...
</div>
`;

chatWindow.innerHTML += typing;
chatWindow.scrollTop = chatWindow.scrollHeight;

const typingHTML = `
<div class="chat-message ai typing-message">
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
</div>
`;

chatWindow.innerHTML += typingHTML;
chatWindow.scrollTop = chatWindow.scrollHeight;

    if(data.success){
        document.querySelector(".typing-message").remove();
        const typing = document.querySelector(".typing-message");

if(typing){
    typing.remove();
}

        chatWindow.innerHTML += `
            <div class="chat-message user">
                ${data.user}
            </div>

            <div class="chat-message ai">
                🤖 ${data.bot}
            </div>
        `;

        messageBox.value="";

        chatWindow.scrollTop = chatWindow.scrollHeight;

    }else{

        alert(data.message);

    }

});

</script>