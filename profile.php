<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT full_name,email,profile_image,created_at
FROM users
WHERE id=?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="card shadow-lg border-0 rounded-4 p-5 text-center">

<img
src="assets/uploads/profiles/<?php echo $user['profile_image']; ?>"
class="rounded-circle mb-4"
width="160"
height="160"
style="object-fit:cover;">

<h2>
<?php echo $user['full_name']; ?>
</h2>

<p class="text-muted">
<?php echo $user['email']; ?>
</p>

<p>
📅 Joined:
<?php echo date("d M Y",strtotime($user['created_at'])); ?>
</p>
<form action="api/profile_upload.php" method="POST" enctype="multipart/form-data" class="mt-4">

    <input
        type="file"
        name="profile"
        class="form-control mb-3"
        accept=".jpg,.jpeg,.png,.webp"
        required>

    <button
        type="submit"
        name="upload"
        class="btn btn-primary">
        Upload Profile Photo
    </button>

</form>