
<?php
session_start();
if (isset($_GET['mood'])) {

    echo "
    <script>
        alert('Mood Saved Successfully!');
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    ";

}

if (isset($_GET['journal'])) {

    echo "
    <script>
        alert('Journal Saved Successfully!');
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
    ";

}

?>




<!DOCTYPE html>
<html lang="en">

<link
href="https://unpkg.com/aos@2.3.4/dist/aos.css"
rel="stylesheet">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindCare AI</title>
<!--- Google Fonts --->
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet"> 
    

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

   <?php include 'includes/navbar.php'; ?>
   <?php include 'includes/hero.php'; ?>
   <?php include 'includes/stats.php'; ?>
   <?php include 'includes/about.php'; ?>
   <?php include 'includes/features.php'; ?>
   <?php include 'includes/testimonials.php'; ?>
   <?php include 'includes/faq.php'; ?>
   <section class="py-5 text-center">

    <h2 class="mb-4">Ready to Talk?</h2>

    <p class="mb-4">
        Start a private conversation with your personal AI therapist.
    </p>

    <a href="chat.php" class="btn btn-primary btn-lg">
        💬 Start AI Chat
    </a>

</section>
   <?php include 'includes/mood.php'; ?>
   <?php include 'includes/journal.php'; ?>
   <?php include 'includes/dashboard.php'; ?>

<script>

fetch("api/dashboard_data.php")
.then(res => res.json())
.then(data => {

    if(data.success){

        document.getElementById("aiInsight").innerText =
        data.insight;

    }

})
.catch(err => console.error(err));

fetch("api/mood_chart.php")
.then(res => res.json())
.then(chart => {

    if(!chart.success) return;

    const ctx = document.getElementById("moodChart");

    new Chart(ctx,{

        type:"bar",

        data:{
            labels:chart.labels,

            datasets:[{
                label:"Mood Count",
                data:chart.data,
                borderWidth:1
            }]
        },

        options:{
            responsive:true,

            plugins:{
                legend:{
                    display:false
                }
            },

            scales:{
                y:{
                    beginAtZero:true
                }
            }
        }

    });

});

</script>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
AOS.init({
    duration:1000,
    once:true
});
</script>

<script>

const themeBtn = document.getElementById("themeToggle");

// Load saved theme
if(localStorage.getItem("theme") === "dark"){

    document.body.classList.add("dark-mode");

    if(themeBtn){
        themeBtn.innerHTML = "☀️ Light";
    }

}

if(themeBtn){

    themeBtn.addEventListener("click",function(){

        document.body.classList.toggle("dark-mode");

        if(document.body.classList.contains("dark-mode")){

            localStorage.setItem("theme","dark");

            themeBtn.innerHTML = "☀️ Light";

        }else{

            localStorage.setItem("theme","light");

            themeBtn.innerHTML = "🌙 Dark";

        }

    });

}

</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>

const counters = document.querySelectorAll(".counter");

const observer = new IntersectionObserver((entries)=>{

    entries.forEach(entry=>{

        if(entry.isIntersecting){

            const counter = entry.target;

            const target = +counter.dataset.target;

            let count = 0;

            const speed = target / 100;

            const updateCounter = ()=>{

                if(count < target){

                    count += speed;

                    counter.innerText = Math.ceil(count).toLocaleString();

                    requestAnimationFrame(updateCounter);

                }else{

                    counter.innerText = target.toLocaleString() + "+";

                }

            };

            updateCounter();

            observer.unobserve(counter);

        }

    });

});

counters.forEach(counter=>{

    observer.observe(counter);

});

</script>
<button id="topBtn">
    ↑
</button>

<script>

const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", function(){

    if(window.scrollY > 300){

        topBtn.style.display = "flex";

    }else{

        topBtn.style.display = "none";

    }

});

topBtn.addEventListener("click", function(){

    window.scrollTo({

        top:0,

        behavior:"smooth"

    });

});

</script>

<script>

fetch("api/mood_prediction.php")
.then(response => response.json())
.then(data => {

    document.getElementById("moodPrediction").innerText =
        data.prediction;

})
.catch(error => {

    console.error(error);

    document.getElementById("moodPrediction").innerText =
        "Unable to generate prediction.";

});

</script>

</body>

</html>
