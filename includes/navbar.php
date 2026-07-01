<?php

include 'auth/config/db.php';

$profileImage = "default.png";

if(isset($_SESSION['user_id'])){

    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    if(!empty($result['profile_image'])){
        $profileImage = $result['profile_image'];
    }

}
?>


<!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">

            <a class="navbar-brand fw-bold text-primary" href="#">
                MindCare AI
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
            
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>

                </ul>

      <?php if(isset($_SESSION['full_name'])) { ?>
      <button
    id="themeToggle"
    class="btn btn-outline-dark me-3">

    🌙 Dark

</button>     


<div class="dropdown ms-3">

    <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
       href="#"
       data-bs-toggle="dropdown">

        <img
            src="assets/uploads/profiles/<?php echo $profileImage; ?>"
            width="45"
            height="45"
            class="rounded-circle border border-2 border-primary"
            style="object-fit:cover;">

        <span class="ms-2 fw-semibold text-dark">
            <?php echo $_SESSION['full_name']; ?>
        </span>

    </a>

    <ul class="dropdown-menu dropdown-menu-end shadow">

        <li>
            <a class="dropdown-item" href="profile.php">
                👤 My Profile
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="dashboard.php">
                📊 Dashboard
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="journal.php">
                📖 Journal
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="mood.php">
                😊 Mood Tracker
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a class="dropdown-item text-danger" href="auth/logout.php">
                🚪 Logout
            </a>
        </li>

    </ul>

</div>

<?php } else { ?>

    <a href="auth/login.php" class="btn btn-outline-primary ms-3">
        Login
    </a>

    <a href="auth/register.php" class="btn btn-primary ms-2">
        Register
    </a>

<?php } ?>
             
                

            </div>

        </div>
    </nav>