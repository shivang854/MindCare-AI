<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include 'auth/config/db.php';

if(isset($_POST['save_mood'])){

    $user_id = $_SESSION['user_id'];

    $mood = $_POST['mood'];

    $stmt = $conn->prepare("INSERT INTO moods(user_id,mood) VALUES(?,?)");

    $stmt->bind_param("is",$user_id,$mood);

    $stmt->execute();


    echo "<script>
window.location='index.php?mood=saved';
</script>";
exit();

}

?>


<section class="mood-section py-5">

<div class="container">

<div class="row align-items-center">

<!-- Left Side -->

<div class="col-lg-6">

<div class="hero-badge">

📊 Mood Tracker

</div>

<h2 class="display-5 fw-bold mt-4">

Track Your Mood,
Every Single Day

</h2>

<p class="lead mt-4">

Understand your emotions with AI-powered mood tracking.
View weekly insights and improve your mental wellness.

</p>

<a href="#" class="primary-btn mt-4">

Track Mood →

</a>

</div>

<!-- Right Side -->

<div class="col-lg-6">

<div class="mood-phone">

<div class="phone-header">

Today's Mood

</div>

<form method="POST">

<div class="text-center my-4">

<input type="hidden" name="mood" id="selectedMood">

<button type="button" class="btn btn-light fs-1 mood-btn" data-mood="Happy">
😊
</button>

<button type="button" class="btn btn-light fs-1 mood-btn" data-mood="Excited">
😄
</button>

<button type="button" class="btn btn-light fs-1 mood-btn" data-mood="Normal">
😐
</button>

<button type="button" class="btn btn-light fs-1 mood-btn" data-mood="Sad">
😔
</button>

<button type="button" class="btn btn-light fs-1 mood-btn" data-mood="Angry">
😡
</button>

</div>

<div class="text-center">

<h4 id="showMood">
Select Your Mood
</h4>

<button
name="save_mood"
class="btn btn-primary px-5 mt-3">

Save Mood

</button>

</div>

</form>


<div class="progress">

<div class="progress-bar bg-primary"

style="width:92%">

92%

</div>

</div>

<div class="graph">

<div class="bar" style="height:60%"></div>

<div class="bar" style="height:85%"></div>

<div class="bar" style="height:45%"></div>

<div class="bar" style="height:95%"></div>

<div class="bar" style="height:70%"></div>

<div class="bar" style="height:90%"></div>

<div class="bar" style="height:65%"></div>

</div>

</div>

</div>

</div>

</div>

</section>
<script>

const moodButtons = document.querySelectorAll(".mood-btn");

const moodInput = document.getElementById("selectedMood");

const showMood = document.getElementById("showMood");

moodButtons.forEach(btn=>{

btn.addEventListener("click",()=>{

moodButtons.forEach(b=>b.classList.remove("btn-primary"));

moodButtons.forEach(b=>b.classList.add("btn-light"));

btn.classList.remove("btn-light");

btn.classList.add("btn-primary");

moodInput.value = btn.dataset.mood;

showMood.innerHTML="Selected Mood : "+btn.dataset.mood;

});

});

</script>