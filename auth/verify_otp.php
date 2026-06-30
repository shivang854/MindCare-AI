<?php
session_start();

$message = "";

if(isset($_POST['verify'])){

    $otp = trim($_POST['otp']);

    if(isset($_SESSION['otp']) && $otp == $_SESSION['otp']){

        header("Location: reset_password.php");
        exit();

    }else{

        $message = "Invalid OTP.";

    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow p-4">

<h3 class="mb-4">Verify OTP</h3>

<?php if($message!=""){ ?>
<div class="alert alert-danger">
<?php echo $message; ?>
</div>
<?php } ?>

<form method="POST">

<input
type="text"
name="otp"
class="form-control mb-3"
placeholder="Enter OTP"
required>

<button
type="submit"
name="verify"
class="btn btn-primary w-100">

Verify OTP

</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>