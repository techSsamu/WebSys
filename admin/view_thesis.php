<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../forms/login.php');
    exit;
}

$thesis_id = $_GET['id'] ?? null;


$stmt = $pdo->prepare("SELECT t.*, u.name as student_name, u.email as student_email, f.file_path 
                       FROM thesis t 
                       JOIN users u ON t.student_id = u.id 
                       LEFT JOIN files f ON t.id = f.thesis_id 
                       WHERE t.id = ?");
$stmt->execute([$thesis_id]);
$thesis = $stmt->fetch();

if (!$thesis) { die("Submission not found."); }


$commentStmt = $pdo->prepare("SELECT c.*, u.name as reviewer_name FROM comments c JOIN users u ON c.reviewer_id = u.id WHERE c.thesis_id = ? ORDER BY c.commented_at DESC");
$commentStmt->execute([$thesis_id]);
$comments = $commentStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thesis Details | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Research Metadata</h4>
                    <a href="all_submissions.php" class="btn btn-sm btn-outline-light">Close View</a>
                </div>
                <div class="card-body p-5 bg-white">
                    <div class="row mb-5">
                        <div class="col-md-8">
                            <h2 class="fw-bold"><?= htmlspecialchars($thesis['title']) ?></h2>
                            <p class="text-muted">By: <?= htmlspecialchars($thesis['student_name']) ?> (<?= $thesis['student_email'] ?>)</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-<?= $thesis['status'] == 'approved' ? 'success' : ($thesis['status'] == 'rejected' ? 'danger' : 'warning') ?> p-2 px-3 fs-6">
                                <?= strtoupper($thesis['status']) ?>
                            </span>
                        </div>
                    </div>

                    <h5>Abstract</h5>
                    <p class="text-justify lh-lg mb-4"><?= nl2br(htmlspecialchars($thesis['abstract'])) ?></p>
                    
                    <div class="mb-5">
                        <span class="fw-bold">Keywords:</span> <?= htmlspecialchars($thesis['keywords']) ?>
                    </div>

                    <hr>

                    <h5 class="mt-4 fw-bold">Reviewer Feedback History</h5>
                    <?php if(empty($comments)): ?>
                        <p class="text-muted small">No evaluations have been recorded yet.</p>
                    <?php else: ?>
                        <?php foreach($comments as $c): ?>
                        <div class="bg-light p-3 rounded mb-3 border-start border-4 border-primary">
                            <div class="d-flex justify-content-between">
                                <strong><?= htmlspecialchars($c['reviewer_name']) ?></strong>
                                <small class="text-muted"><?= date('M d, Y', strtotime($c['commented_at'])) ?></small>
                            </div>
                            <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if($thesis['file_path']): ?>
                    <div class="mt-5 text-center p-4 border rounded-3 bg-light">
                        <h6>Full Research Document Attached</h6>
                        <a href="../<?= $thesis['file_path'] ?>" class="btn btn-danger mt-2" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Open PDF File
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>