<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'];
$moods = ["Happy", "Sad", "Stress", "Angry", "Neutral"];

$moodCount = [];

foreach($moods as $mood){

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM chats
        WHERE user_id=? AND mood=?
    ");

    $stmt->bind_param("is", $user_id, $mood);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    $moodCount[$mood] = $result['total'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>Mood Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
    <meta charset="UTF-8">
    <title>Mood Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-4">📊 Mood Analytics Dashboard</h2>

 
    

    <div class="row g-4 mb-4">

<div class="col-md-3">
<div class="card shadow border-0 text-center p-3">
<h3>😊</h3>
<h5>Happy</h5>
<h2><?php echo $moodCount['Happy']; ?></h2>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0 text-center p-3">
<h3>😔</h3>
<h5>Sad</h5>
<h2><?php echo $moodCount['Sad']; ?></h2>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0 text-center p-3">
<h3>😰</h3>
<h5>Stress</h5>
<h2><?php echo $moodCount['Stress']; ?></h2>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0 text-center p-3">
<h3>😡</h3>
<h5>Angry</h5>
<h2><?php echo $moodCount['Angry']; ?></h2>
</div>
</div>

</div>

    
    <div class="card p-4 shadow">

    <h4 class="mb-3">Mood Summary</h4>

    <table class="table table-bordered">

        <tr>
            <th>😊 Happy</th>
            <td><?php echo $moodCount['Happy']; ?></td>
        </tr>

        <tr>
            <th>😔 Sad</th>
            <td><?php echo $moodCount['Sad']; ?></td>
        </tr>

        <tr>
            <th>😰 Stress</th>
            <td><?php echo $moodCount['Stress']; ?></td>
        </tr>

        <tr>
            <th>😡 Angry</th>
            <td><?php echo $moodCount['Angry']; ?></td>
        </tr>

        <tr>
            <th>😐 Neutral</th>
            <td><?php echo $moodCount['Neutral']; ?></td>
        </tr>

    </table>

</div>
<div class="card mt-4 p-4 shadow">

    <h4 class="mb-3">Mood Distribution</h4>

    <div style="width:400px; height:400px; margin:auto;">
    <canvas id="moodChart"></canvas>
</div>
    

</div>

</div>
<script>


const ctx = document.getElementById('moodChart');

new Chart(ctx, {

    type: 'pie',

    data: {

        labels: [
            'Happy',
            'Sad',
            'Stress',
            'Angry',
            'Neutral'
        ],

     
        datasets: [{
    data: [
        <?php echo $moodCount['Happy']; ?>,
        <?php echo $moodCount['Sad']; ?>,
        <?php echo $moodCount['Stress']; ?>,
        <?php echo $moodCount['Angry']; ?>,
        <?php echo $moodCount['Neutral']; ?>
    ],
    backgroundColor: [
        '#4ade80',
        '#60a5fa',
        '#facc15',
        '#f87171',
        '#a78bfa'
    ],
    borderWidth: 2
}]

    },

    options: {

    responsive: true,
    maintainAspectRatio: false,

    plugins: {

        legend: {
            position: 'bottom'
        }

    }

}

});

</script>
</body>
</html>