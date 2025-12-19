<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
    header('Location: ../forms/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: notifications.php');
    exit;
}

$thesis_id = $_GET['id'];

// Fetch Thesis details + Student Profile Info
$stmt = $pdo->prepare("
    SELECT t.*, u.name as student_name, u.profile_pic, u.email as student_email 
    FROM thesis t 
    JOIN users u ON t.student_id = u.id 
    WHERE t.id = ?
");
$stmt->execute([$thesis_id]);
$thesis = $stmt->fetch();

if (!$thesis) {
    die("Thesis not found.");
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $comment = $_POST['comment'];
    $reviewer_id = $_SESSION['user_id'];

    $pdo->beginTransaction();
    try {
        // 1. Update Thesis Status
        $updateStmt = $pdo->prepare("UPDATE thesis SET status = ? WHERE id = ?");
        $updateStmt->execute([$status, $thesis_id]);

        // 2. Insert Comment
        $commentStmt = $pdo->prepare("INSERT INTO comments (thesis_id, reviewer_id, comment) VALUES (?, ?, ?)");
        $commentStmt->execute([$thesis_id, $reviewer_id, $comment]);

        $pdo->commit();
        header('Location: notifications.php?msg=success');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error saving evaluation: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Thesis | Reviewer Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; }
        .evaluation-card { border: none; border-radius: 15px; }
        .student-header { background: #1a1c2e; color: white; border-radius: 15px 15px 0 0; }
        .profile-img { width: 60px; height: 60px; object-fit: cover; border: 2px solid #1cc88a; }
        .abstract-box { background: #fff; border-left: 5px solid #1cc88a; padding: 20px; font-style: italic; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <a href="notifications.php" class="btn btn-link text-decoration-none text-muted mb-3 p-0">
                <i class="bi bi-arrow-left"></i> Back to Queue
            </a>

            <div class="card evaluation-card shadow-sm">
                <div class="student-header p-4">
                    <div class="d-flex align-items-center">
                        <img src="../<?= htmlspecialchars($thesis['profile_pic'] ?: 'profiles/default.png') ?>" class="rounded-circle profile-img me-3">
                        <div>
                            <h5 class="mb-0 fw-bold"><?= htmlspecialchars($thesis['student_name']) ?></h5>
                            <small class="opacity-75"><?= htmlspecialchars($thesis['student_email']) ?></small>
                        </div>
                        <div class="ms-auto text-end">
                            <small class="d-block opacity-50">Submitted on</small>
                            <span class="fw-bold"><?= date('M d, Y', strtotime($thesis['uploaded_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4"><?= htmlspecialchars($thesis['title']) ?></h2>
                    
                    <div class="mb-4">
                        <h6 class="text-uppercase fw-bold text-success small">Technical Adviser</h6>
                        <p class="fs-5"><?= htmlspecialchars($thesis['adviser']) ?></p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase fw-bold text-success small">Research Abstract</h6>
                        <div class="abstract-box shadow-sm">
                            <?= nl2br(htmlspecialchars($thesis['abstract'])) ?>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold text-success small">Keywords</h6>
                        <p class="text-muted"><?= htmlspecialchars($thesis['keywords']) ?></p>
                    </div>

                    <hr class="my-5">

                    <form method="POST" class="bg-light p-4 rounded-4">
                        <h4 class="fw-bold mb-4">Your Evaluation</h4>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Decision</label>
                            <div class="d-flex gap-3">
                                <input type="radio" class="btn-check" name="status" id="approve" value="approved" required>
                                <label class="btn btn-outline-success px-4 rounded-pill" for="approve">
                                    <i class="bi bi-check-circle me-1"></i> Approve
                                </label>

                                <input type="radio" class="btn-check" name="status" id="reject" value="rejected">
                                <label class="btn btn-outline-danger px-4 rounded-pill" for="reject">
                                    <i class="bi bi-x-circle me-1"></i> Reject / Revise
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Feedback / Comments</label>
                            <textarea name="comment" class="form-control" rows="5" placeholder="Explain your decision to the student..." required></textarea>
                            <div class="form-text">This comment will be visible to the student.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill shadow">
                                Submit Final Evaluation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>