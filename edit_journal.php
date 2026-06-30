<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
SELECT *
FROM journals
WHERE id=? AND user_id=?
");

$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){
    die("Journal not found.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Journal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<h2 class="mb-4">
✏️ Edit Journal
</h2>

<form action="api/update_journal.php" method="POST">

<input
type="hidden"
name="id"
value="<?php echo $row['id']; ?>">

<div class="mb-3">

<label class="form-label">
Title
</label>

<input
type="text"
name="title"
class="form-control"
value="<?php echo htmlspecialchars($row['title']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">
Journal
</label>

<textarea
name="content"
class="form-control"
rows="8"
required><?php echo htmlspecialchars($row['content']); ?></textarea>

</div>

<button class="btn btn-success">
Update Journal
</button>

<a href="journal.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>

</body>
</html>