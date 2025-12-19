<?php
session_start();
require '../db.php';
if(!isset($_SESSION['user_id'])) die("Access denied");

$keyword = $_GET['keyword'] ?? '';

$results = [];
if($keyword){
    $stmt = $pdo->prepare("SELECT t.*, u.name AS author_name FROM thesis t 
                           JOIN users u ON t.author_id=u.id
                           WHERE t.title LIKE ? OR t.abstract LIKE ? OR t.keywords LIKE ?");
    $param = "%$keyword%";
    $stmt->execute([$param,$param,$param]);
    $results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Full-Text Search</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
<h3>Full-Text Thesis Search</h3>

<form method="GET" class="mb-3">
<div class="input-group">
<input type="text" name="keyword" class="form-control" placeholder="Enter keyword" value="<?=htmlspecialchars($keyword)?>" required>
<button class="btn btn-primary">Search</button>
</div>
</form>

<?php if($keyword && count($results)==0): ?>
<p>No results found.</p>
<?php elseif($keyword): ?>
<table class="table table-striped">
<thead><tr><th>Title</th><th>Author</th><th>Year</th><th>Adviser</th><th>Keywords</th><th>Download</th></tr></thead>
<tbody>
<?php foreach($results as $r): ?>
<tr>
<td><?=htmlspecialchars($r['title'])?></td>
<td><?=htmlspecialchars($r['author_name'])?></td>
<td><?=$r['year']?></td>
<td><?=htmlspecialchars($r['adviser'])?></td>
<td><?=htmlspecialchars($r['keywords'])?></td>
<td>
<?php if(file_exists('../uploads/'.$r['file_path'])): ?>
<a href="../uploads/<?=basename($r['file_path'])?>" class="btn btn-sm btn-primary" download>Download</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
