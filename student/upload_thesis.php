<?php
session_start();
require '../db.php'; // Path to root db.php

// Access Control: Only students can upload
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../forms/login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = $_SESSION['user_id'];
    $title    = trim($_POST['title']);
    $abstract = trim($_POST['abstract']);
    $keywords = trim($_POST['keywords']);
    $adviser  = trim($_POST['adviser']);

    // File Handling
    $file_name = $_FILES['thesis_file']['name'];
    $file_tmp  = $_FILES['thesis_file']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validation
    if ($file_ext !== 'pdf') {
        $error = "Error: Only PDF documents are accepted.";
    } elseif ($_FILES['thesis_file']['size'] > 10 * 1024 * 1024) { // 10MB Limit
        $error = "Error: File size must be less than 10MB.";
    } else {
        // Define directory relative to this file
        $upload_dir = '../uploads/'; 
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        // Create a unique filename to prevent overwriting
        $unique_file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
        $db_save_path = 'uploads/' . $unique_file_name;

        // Attempt to move file
        if (move_uploaded_file($file_tmp, $upload_dir . $unique_file_name)) {
            try {
                $pdo->beginTransaction();

                // 1. Insert Metadata into 'thesis' table
                $stmt = $pdo->prepare("INSERT INTO thesis (student_id, title, abstract, keywords, adviser, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $stmt->execute([$user_id, $title, $abstract, $keywords, $adviser]);
                $thesis_id = $pdo->lastInsertId();

                // 2. Insert File Reference into 'files' table
                $stmt2 = $pdo->prepare("INSERT INTO files (thesis_id, file_name, file_path, version) VALUES (?, ?, ?, 1)");
                $stmt2->execute([$thesis_id, $file_name, $db_save_path]);

                $pdo->commit();
                $success = "Thesis submitted successfully! Redirecting to dashboard...";
                header("refresh:2;url=student.php"); // Automatically redirect back
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Database Error: " . $e->getMessage();
            }
        } else {
            $error = "Failed to upload file. Please check folder permissions.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Submission | Thesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; }
        .form-label { font-weight: 600; color: #4e73df; font-size: 0.85rem; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="mb-0 fw-bold">Submit New Research</h4>
                    <p class="text-muted small">Fill in the metadata and upload your PDF file.</p>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 small"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if($success): ?>
                        <div class="alert alert-success border-0 small"><?= $success ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Thesis Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter the complete title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Abstract / Summary</label>
                            <textarea name="abstract" class="form-control" rows="5" placeholder="Provide a brief overview of your study..." required></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Keywords</label>
                                <input type="text" name="keywords" class="form-control" placeholder="e.g., Blockchain, Education, PH">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Technical Adviser</label>
                                <input type="text" name="adviser" class="form-control" placeholder="Name of your adviser" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Final Document (PDF Only)</label>
                            <div class="input-group">
                                <input type="file" name="thesis_file" class="form-control" id="inputGroupFile02" accept=".pdf" required>
                            </div>
                            <div class="form-text mt-2">Maximum file size: 10MB.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="student.php" class="text-decoration-none text-muted small">← Back to Submissions</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">Upload Thesis</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>