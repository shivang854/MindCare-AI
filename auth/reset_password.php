<?php
session_start();
include 'config/db.php';

$message = "";

if(!isset($_SESSION['email'])){
    header("Location: forgot_password.php");
    exit();
}

if(isset($_POST['reset'])){

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password != $confirm){

        $message = "Passwords do not match.";

    }else{

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
        UPDATE users
        SET password=?
        WHERE email=?
        ");

        $stmt->bind_param("ss", $hash, $_SESSION['email']);
        $stmt->execute();

        unset($_SESSION['otp']);
        unset($_SESSION['email']);

        header("Location: login.php?reset=success");
        exit();

    }

}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Reset Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow p-4">

<h3 class="mb-4">Reset Password</h3>

<?php if($message!=""){ ?>

<div class="alert alert-danger">

<?php echo $message; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label>New Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Confirm Password</label>

<input
type="password"
name="confirm"
class="form-control"
required>

</div>

<button
type="submit"
name="reset"
class="btn btn-success w-100">

Reset Password

</button>

</form>

</div>

</div>

</div>

</div>

</body>

</html>