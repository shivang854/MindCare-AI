<?php

session_start();

include "../auth/config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Conversation not found.");
}

$conversation_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$check = $conn->prepare("
SELECT id,title
FROM conversations
WHERE id=? AND user_id=?
");

$check->bind_param("ii",$conversation_id,$user_id);
$check->execute();

$result = $check->get_result();

if($result->num_rows==0){
    die("Conversation not found.");
}

$chat = $result->fetch_assoc();
if(isset($_POST['title'])){

    $title = trim($_POST['title']);

    if($title != ""){

        $update = $conn->prepare("
        UPDATE conversations
        SET title=?
        WHERE id=? AND user_id=?
        ");

        $update->bind_param(
            "sii",
            $title,
            $conversation_id,
            $user_id
        );

        $update->execute();

    }

    header("Location: ../chat.php?conversation=".$conversation_id);
    exit();

}


?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Rename Chat</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card p-4">

<h3 class="mb-4">Rename Conversation</h3>

<form method="POST">

<input
type="text"
name="title"
class="form-control mb-3"
value="<?php echo htmlspecialchars($chat['title']); ?>"
required>

<button class="btn btn-primary">
    <?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    echo "POST RECEIVED";
}
?>
Save
</button>

<a href="../chat.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>

</div>

</body>

</html>