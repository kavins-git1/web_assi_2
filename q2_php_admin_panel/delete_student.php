<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 - Web Technology | Assignment II - Q2
 * 
 * Delete Student Record & Cleanup Uploaded Files from Disk
 */

require_once 'config/db.php';

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($studentId > 0) {
    $pdo = getDBConnection();

    try {
        $pdo->beginTransaction();

        // 1. Fetch file paths to delete from disk
        $docStmt = $pdo->prepare("SELECT file_path FROM student_documents WHERE student_id = ?");
        $docStmt->execute([$studentId]);
        $files = $docStmt->fetchAll();

        foreach ($files as $file) {
            if (!empty($file['file_path']) && file_exists($file['file_path'])) {
                @unlink($file['file_path']);
            }
        }

        // 2. Delete Student Record (Cascade deletes records in student_documents)
        $delStmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
        $delStmt->execute([$studentId]);

        $pdo->commit();
        header("Location: index.php?msg=" . urlencode("Student record and associated files successfully deleted."));
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: index.php?error=" . urlencode("Delete Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php?error=" . urlencode("Invalid Student ID provided."));
    exit;
}
?>
