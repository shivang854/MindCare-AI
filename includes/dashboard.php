<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'] ?? 0;

// Total Chats
$chat = $conn->prepare("
SELECT COUNT(*) total
FROM chats
WHERE user_id=?
");

$chat->bind_param("i",$user_id);
$chat->execute();

$totalChats = $chat->get_result()->fetch_assoc()['total'];

// Total Journals
$journal = $conn->prepare("
SELECT COUNT(*) total
FROM journals
WHERE user_id=?
");

$journal->bind_param("i",$user_id);
$journal->execute();

$totalJournals = $journal->get_result()->fetch_assoc()['total'];

// Most Common Mood
$mood = $conn->prepare("
SELECT mood,COUNT(*) total
FROM chats
WHERE user_id=?
GROUP BY mood
ORDER BY total DESC
LIMIT 1
");

$mood->bind_param("i",$user_id);
$mood->execute();

$moodData = $mood->get_result()->fetch_assoc();

$topMood = $moodData['mood'] ?? "No Data";

// Highest Risk
$risk = $conn->prepare("
SELECT risk_level
FROM chats
WHERE user_id=?
ORDER BY FIELD(risk_level,'Critical','High','Medium','Low')
LIMIT 1
");

$risk->bind_param("i",$user_id);
$risk->execute();

$riskLevel = $risk->get_result()->fetch_assoc()['risk_level'] ?? "Low";

?>



<section class="dashboard-section py-5">

<div class="container">

    <div class="text-center mb-5">
        <div class="hero-badge">
            📊 Progress Dashboard
        </div>

        <h2 class="display-5 fw-bold mt-4">
            Track Your Mental Wellness Journey
        </h2>

        <p class="lead">
            AI continuously analyzes your progress and gives personalized insights.
        </p>
    </div>
    <div class="row g-4">

    <!-- Card 1 -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-icon">🔥</div>
            
            <h3><?php echo $totalChats; ?></h3>

<p>Total Chats</p>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-icon">😊</div>
            <h3><?php echo $topMood; ?></h3>

<p>Most Common Mood</p>
            
        </div>
    </div>

    <!-- Card 3 -->
    <div class="col-lg-3 col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-icon">🧠</div>
            <h3><?php echo $totalJournals; ?></h3>

<p>Journals Written</p>
          
            
        </div>
    </div>

 
    <!-- Card 4 -->
<div class="col-lg-3 col-md-6">
    <div class="dashboard-card">
        <div class="dashboard-icon">📈</div>
        <h3><?php echo $riskLevel; ?></h3>

        <p>Highest Risk Level</p>

    </div>
</div>

</div> <!-- row end -->

<!-- 👇 AI Insight Card yahan paste karo -->
<div class="row mt-5">

    <div class="col-12">

        <div class="dashboard-card">

            <h3 class="mb-3">
                🧠 AI Wellness Insight
            </h3>

            <p id="aiInsight">
                Generating personalized insight...
            </p>

        </div>

    </div>

</div>

<div class="row mt-4">

    <div class="col-12">

        <div class="dashboard-card">

            <h3 class="mb-3">
                🔮 AI Mood Prediction
            </h3>

            <p id="moodPrediction">

                Generating prediction...

            </p>

        </div>

    </div>

</div>

<!-- 👇 MOOD GRAPH YAHAN ADD KARO -->
<div class="row mt-4">

    <div class="col-12">

        <div class="dashboard-card">

            <h3 class="mb-4">
                📊 Mood Analytics
            </h3>

            <canvas id="moodChart" height="120"></canvas>

        </div>

    </div>

</div>

</div> <!-- container end -->

</section>