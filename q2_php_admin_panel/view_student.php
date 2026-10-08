<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 - Web Technology | Assignment II - Q2
 * 
 * View Student Profile & Display Uploaded Metadata / Files
 */

require_once 'config/db.php';

$pdo = getDBConnection();
$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: index.php?error=" . urlencode("Student record not found."));
    exit;
}

// Fetch all associated document metadata
$docStmt = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY upload_date DESC");
$docStmt->execute([$studentId]);
$documents = $docStmt->fetchAll();

$profilePhoto = null;
$otherDocs = [];
foreach ($documents as $doc) {
    if ($doc['document_type'] === 'profile_image' && $profilePhoto === null) {
        $profilePhoto = $doc;
    } else {
        $otherDocs[] = $doc;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile View | <?= htmlspecialchars($student['full_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <nav class="navbar">
        <div class="brand">
            <i class="fa-solid fa-id-badge"></i>
            <span>Student Profile Details</span>
        </div>
        <div>
            <a href="index.php" class="btn-admin btn-secondary"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container" style="max-width: 900px;">
        <div class="dashboard-card" style="padding: 2rem;">
            <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: flex-start;">
                
                <!-- Student Photo Card -->
                <div style="text-align: center; width: 220px;">
                    <?php if ($profilePhoto && file_exists($profilePhoto['file_path'])): ?>
                        <img src="<?= htmlspecialchars($profilePhoto['file_path']) ?>" alt="Profile Photo" style="width: 180px; height: 180px; object-fit: cover; border-radius: 16px; border: 3px solid var(--admin-accent); box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div style="width: 180px; height: 180px; border-radius: 16px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #64748b; margin: 0 auto;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h3 style="margin-top: 1rem; color: var(--admin-primary);"><?= htmlspecialchars($student['full_name']) ?></h3>
                    <p style="color: var(--text-muted); font-size: 0.88rem;"><?= htmlspecialchars($student['roll_number']) ?></p>
                </div>

                <!-- Profile Info Details Grid -->
                <div style="flex: 1; min-width: 280px;">
                    <h3 style="font-size: 1.2rem; color: var(--admin-primary); margin-bottom: 1rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem;">
                        <i class="fa-solid fa-graduation-cap" style="color: var(--admin-accent);"></i> Academic Record Information
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; font-size: 0.92rem;">
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Degree Course</span>
                            <strong><?= htmlspecialchars($student['course']) ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Institutional Email</span>
                            <strong><?= htmlspecialchars($student['email']) ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Gender</span>
                            <strong><?= htmlspecialchars($student['gender'] ?? 'N/A') ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Phone</span>
                            <strong><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Admission Status</span>
                            <span class="status-badge <?= $student['admission_status'] === 'Approved' ? 'status-approved' : 'status-pending' ?>">
                                <?= htmlspecialchars($student['admission_status']) ?>
                            </span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Registered On</span>
                            <strong><?= date('d M Y, h:i A', strtotime($student['created_at'])) ?></strong>
                        </div>
                    </div>

                    <!-- Uploaded Documents Metadata List -->
                    <h3 style="font-size: 1.1rem; color: var(--admin-primary); margin-top: 2rem; margin-bottom: 1rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem;">
                        <i class="fa-solid fa-folder-open" style="color: var(--admin-accent);"></i> Uploaded Document Metadata
                    </h3>

                    <?php if (empty($documents)): ?>
                        <p style="color: var(--text-muted);">No documents uploaded for this student.</p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php foreach ($documents as $doc): ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 0.75rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <i class="fa-solid <?= str_contains($doc['file_type'], 'image') ? 'fa-file-image' : 'fa-file-pdf' ?>" style="font-size: 1.4rem; color: var(--admin-accent);"></i>
                                        <div>
                                            <strong style="font-size: 0.9rem; color: var(--admin-primary);"><?= htmlspecialchars($doc['original_name']) ?></strong>
                                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                                Type: <?= htmlspecialchars($doc['file_type']) ?> • 
                                                Size: <?= number_format($doc['file_size'] / 1024, 1) ?> KB • 
                                                Uploaded: <?= date('M d, Y', strtotime($doc['upload_date'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (file_exists($doc['file_path'])): ?>
                                        <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn-admin btn-secondary" style="font-size: 0.8rem;">
                                            <i class="fa-solid fa-download"></i> View / Download
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--danger); font-size: 0.8rem;">File Missing on Disk</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn-admin btn-primary"><i class="fa-solid fa-pen-to-square"></i> Edit Record</a>
                <a href="delete_student.php?id=<?= $student['student_id'] ?>" class="btn-admin btn-danger" onclick="return confirm('Delete student profile?');"><i class="fa-solid fa-trash"></i> Delete</a>
            </div>
        </div>
    </div>

</body>
</html>
