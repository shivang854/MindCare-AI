<?php
include 'config/db.php';
?>
<?php

if(isset($_POST['register'])){

    $full_name = trim($_POST['full_name']);
    if(!preg_match("/^[a-zA-Z ]+$/",$full_name)){

    echo "<script>alert('Name should contain only letters.');</script>";

    exit();

}
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    if(strlen($password) < 8){

    echo "<script>alert('Password must be at least 8 characters long.');</script>";

}else{

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){

        echo "<script>alert('Email already registered!');</script>";

    }else{

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users(full_name,email,password) VALUES(?,?,?)");
        $stmt->bind_param("sss",$full_name,$email,$hashedPassword);

        if($stmt->execute()){

            echo "<script>
alert('Registration Successful!');
window.location='login.php';
</script>";

        }else{

            echo "<script>alert('Something went wrong!');</script>";

        }

    }

}

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MindCare AI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow p-4 rounded-4">

                <h2 class="text-center mb-4">
                    Create Account
                </h2>

                <form method="POST">

                    <div class="mb-3">
                        <label>Full Name</label>
                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            required>
                    </div>

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

                    <button
                        type="submit"
                        name="register"
                        class="btn btn-primary w-100">

                        Register

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>

</html>