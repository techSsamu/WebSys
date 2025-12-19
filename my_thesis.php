<?php
session_start();
require '../db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='student') die("Access denied");

$stmt = $pdo->prepare("SELECT t.*, f.file_path FROM thesis t LEFT JOIN files f ON t.id=f.thesis_id WHERE t.author_id=?");
$stmt->execute([$_SESSION['user_id']]);
$theses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Thesis</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
<h3>My Thesis Submissions</h3>
<table class="table table-striped mt-3">
<tr>
<th>Title</th><th>Year</th><th>Status</th><th>Download</th>
</tr>
<?php foreach($theses as $t): ?>
<tr>
<td><?=htmlspecialchars($t['title'])?></td>
<td><?=$t['year']?></td>
<td><?=$t['status']?></td>
<td>
<?php if($t['file_path'] && file_exists($t['file_path'])): ?>
<a href="../uploads/<?=basename($t['file_path'])?>" class="btn btn-sm btn-primary" download>Download</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
<a href="upload_thesis.php" class="btn btn-success">Upload New Thesis</a>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
