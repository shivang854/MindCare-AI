<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include 'auth/config/db.php';

if(isset($_POST['save_journal'])){

    $user_id = $_SESSION['user_id'];

    $journal = trim($_POST['journal']);

    $stmt = $conn->prepare("INSERT INTO journals(user_id,journal) VALUES(?,?)");

    $stmt->bind_param("is",$user_id,$journal);

    $stmt->execute();

echo "<script>
window.location='index.php?journal=saved';
</script>";
exit();
    
}
?>




<section class="journal-section py-5">

<div class="container">

<div class="row align-items-center">

    <!-- Left Side -->

    <div class="col-lg-6">

        <div class="hero-badge">
            📖 Smart Journal
        </div>

        <h2 class="display-5 fw-bold mt-4">
            Write Your Thoughts,<br>
            Let AI Understand You
        </h2>

        <p class="lead mt-4">
            Maintain your daily journal and let AI analyze your emotions,
            detect patterns and provide personalized mental wellness insights.
        </p>

        <ul class="chat-list">
            <li>✅ Daily Journaling</li>
            <li>✅ AI Emotion Analysis</li>
            <li>✅ Weekly Insights</li>
        </ul>

        <a href="#" class="primary-btn mt-4">
            Start Writing →
        </a>

    </div>

  <div class="col-lg-6">

<div class="journal-card">

<div class="journal-header">
📖 Today's Journal
</div>

<form method="POST">

<div class="mb-3 mt-4">

<textarea
name="journal"
class="form-control"
rows="8"
placeholder="How are you feeling today?"
required></textarea>

</div>

<button
type="submit"
name="save_journal"
class="btn btn-primary w-100">

Save Journal

</button>

</form>

</div>

</div>
    
</div>

</div>

</section>