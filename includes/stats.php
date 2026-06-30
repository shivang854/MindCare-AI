<?php

include 'auth/config/db.php';

// Total Users
$userResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $userResult->fetch_assoc()['total'];

// Total Chats
$chatResult = $conn->query("SELECT COUNT(*) AS total FROM chats");
$totalChats = $chatResult->fetch_assoc()['total'];

// Total Journals
$journalResult = $conn->query("SELECT COUNT(*) AS total FROM journals");
$totalJournals = $journalResult->fetch_assoc()['total'];

?>



<section class="stats-section py-5">

<div class="container">

<div class="row text-center">

<div class="col-lg-3 col-6 mb-4">

<div class="stats-card">

<h2 class="counter" data-target="<?php echo $totalUsers; ?>">0</h2>

<p>👥 Users</p>

</div>

</div>

<div class="col-lg-3 col-6 mb-4">

<div class="stats-card">

<h2 class="counter" data-target="<?php echo $totalChats; ?>">0</h2>

<p>💬 AI Conversations</p>

</div>

</div>

<div class="col-lg-3 col-6 mb-4">

<div class="stats-card">

<h2 class="counter" data-target="<?php echo $totalJournals; ?>">0</h2>

<p>📖 Journals</p>

</div>

</div>

<div class="col-lg-3 col-6 mb-4">

<div class="stats-card">

<h2>24/7</h2>

<p>🕒 AI Support</p>

</div>

</div>

</div>

</div>

</section>