<?php
session_start();
require '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: all_submissions.php');
    exit;
}

$id = $_GET['id'];

// Optional: Logic to delete the physical file from the folder too
$stmt = $pdo->prepare("SELECT file_path FROM files WHERE thesis_id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch();

if ($file && file_exists('../' . $file['file_path'])) {
    unlink('../' . $file['file_path']); // Deletes the actual PDF file
}

// Delete from database (Cascading deletes will handle files/comments if set up in SQL)
$del = $pdo->prepare("DELETE FROM thesis WHERE id = ?");
if ($del->execute([$id])) {
    header("Location: all_submissions.php?msg=deleted");
}
?>