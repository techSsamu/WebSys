<?php
session_start();
require '../db.php';
if(!isset($_SESSION['user_id'])) die("Access denied");

// Initialize filters
$title = $_GET['title'] ?? '';
$author = $_GET['author'] ?? '';
$adviser = $_GET['adviser'] ?? '';
$year = $_GET['year'] ?? '';
$keywords = $_GET['keywords'] ?? '';

// Build query
$query = "SELECT t.*, u.name AS author_name FROM thesis t 
          JOIN users u ON t.author_id=u.id 
          WHERE 1=1";
$params = [];

if($title){
    $query .= " AND t.title LIKE ?";
    $params[] = "%$title%";
}
if($author){
    $query .= " AND u.name LIKE ?";
    $params[] = "%$author%";
}
if($adviser){
    $query .= " AND t.adviser LIKE ?";
    $params[] = "%$adviser%";
}
if($year){
    $query .= " AND t.year = ?";
    $params[] = $year;
}
if($keywords){
    $query .= " AND t.keywords LIKE ?";
    $params[] = "%$keywords%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Search Thesis</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
<h3>Search Thesis</h3>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Title" value="<?=htmlspecialchars($title)?>"></div>
  <div class="col-md-3"><input type="text" name="author" class="form-control" placeholder="Author" value="<?=htmlspecialchars($author)?>"></div>
  <div class="col-md-2"><input type="text" name="adviser" class="form-control" placeholder="Adviser" value="<?=htmlspecialchars($adviser)?>"></div>
  <div class="col-md-2"><input type="number" name="year" class="form-control" placeholder="Year" value="<?=htmlspecialchars($year)?>"></div>
  <div class="col-md-2"><input type="text" name="keywords" class="form-control" placeholder="Keywords" value="<?=htmlspecialchars($keywords)?>"></div>
  <div class="col-md-12 mt-2"><button class="btn btn-primary">Search</button></div>
</form>

<?php if(count($results)==0): ?>
<p>No results found.</p>
<?php else: ?>
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
