<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit();
}

include 'auth/config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT *
FROM journals
WHERE user_id=?
ORDER BY created_at DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Journal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<h2 class="mb-4">📝 My Journal</h2>

<a href="chat.php" class="btn btn-secondary mb-3">
← Back to Chat
</a>

<div class="card shadow p-4 mb-4">

<h4>Add Journal</h4>

<form action="api/save_journal.php" method="POST">

<div class="mb-3">

<label class="form-label">Title</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Journal</label>

<textarea
name="content"
class="form-control"
rows="6"
required></textarea>

</div>

<button class="btn btn-primary">
Save Journal
</button>

</form>

</div>

<h4 class="mb-3">
Previous Journals
</h4>

<?php while($row = $result->fetch_assoc()){ ?>

<div class="card shadow mb-3">

<div class="card-body">

<h5>
<?php echo htmlspecialchars($row['title']); ?>
</h5>

<p>
<?php echo nl2br(htmlspecialchars($row['content'])); ?>
</p>

<small class="text-muted">
<?php echo $row['created_at']; ?>
</small>
<div class="mt-3">

<a
href="api/delete_journal.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this journal?');">

🗑 Delete
<a
href="edit_journal.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">

✏️ Edit

</a>
</a>

</div>

</div>

</div>

<?php } ?>

</div>

</body>
</html>