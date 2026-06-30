<?php
session_start();
include 'config/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if(isset($_POST['send'])){

    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

       
    $otp = rand(100000,999999);

$_SESSION['otp'] = $otp;
$_SESSION['email'] = $email;

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = $_ENV['SMTP_EMAIL'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom($_ENV['SMTP_EMAIL'], 'MindCare AI');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "MindCare AI - Password Reset OTP";

    $mail->Body = "
    <h2>Password Reset</h2>

    <p>Your OTP is:</p>

    <h1>{$otp}</h1>

    <p>This OTP will be used to reset your password.</p>
    ";

    $mail->send();

    header("Location: verify_otp.php");
    exit();

}catch(Exception $e){

    $message = "Failed to send OTP. ".$mail->ErrorInfo;

}

        // next step me we will send email via SMTP

    }else{
        $message = "Email not found!";
    }

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Forgot Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card p-4 shadow">

<h3 class="mb-3">Forgot Password</h3>

<?php if($message!=""){ ?>
<div class="alert alert-info">
<?php echo $message; ?>
</div>
<?php } ?>

<form method="POST">

<input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>

<button type="submit" name="send" class="btn btn-primary w-100">
Send OTP
</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>