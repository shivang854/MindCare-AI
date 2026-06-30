<?php
session_start();
include 'config/db.php';

$message = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $user = $result->fetch_assoc();

        if(password_verify($password,$user['password'])){

    session_regenerate_id(true);

    

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];

            header("Location: ../index.php");
            exit();

        }else{

            $message ="Invalid email or password.";

        }

    }else{

        $message = "Invalid email or password.";

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | MindCare AI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow rounded-4 p-4">

<h2 class="text-center mb-4">Login</h2>
<?php if(isset($_GET['reset'])){ ?>

<div class="alert alert-success">
Password reset successfully. Please login.
</div>

<?php } ?>

<?php if($message!=""){ ?>

<div class="alert alert-danger">
<?php echo $message; ?>
</div>

<?php } ?>

<form method="POST">

<div class="mb-3">
<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>
<div class="text-end mb-3">

    <a href="forgot_password.php" class="text-decoration-none">
        Forgot Password?
    </a>

</div>

<button
type="submit"
name="login"
class="btn btn-primary w-100">

Login

</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>