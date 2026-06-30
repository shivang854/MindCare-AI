
<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT trusted_name,trusted_phone,trusted_email
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

<title>Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">
    <?php
if(isset($_GET['saved'])){
?>
<div class="alert alert-success">
    ✅ Trusted Contact Saved Successfully.
</div>
<?php
}
?>


<h2 class="mb-4">
👤 Trusted Contact
</h2>

<a href="chat.php" class="btn btn-secondary mb-4">
← Back
</a>

<form action="api/save_profile.php" method="POST">

<div class="card shadow p-4">

<div class="mb-3">

<label>Name</label>

<input
type="text"
name="trusted_name"
class="form-control"
value="<?php echo htmlspecialchars($user['trusted_name'] ?? ''); ?>">

</div>

<div class="mb-3">

<label>Phone</label>

<input
type="text"
name="trusted_phone"
class="form-control"
value="<?php echo htmlspecialchars($user['trusted_phone'] ?? ''); ?>">

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="trusted_email"
class="form-control"
value="<?php echo htmlspecialchars($user['trusted_email'] ?? ''); ?>">

</div>

<button class="btn btn-primary">
Save
</button>

</div>

</form>

</div>

</body>

</html>