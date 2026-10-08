<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 - Web Technology | Assignment II - Q2
 * 
 * Edit Student Details & Update Uploaded File Metadata
 */

require_once 'config/db.php';

$pdo = getDBConnection();
$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: index.php?error=" . urlencode("Student record not found."));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName   = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $course     = trim($_POST['course'] ?? '');
    $gender     = trim($_POST['gender'] ?? 'Male');
    $status     = trim($_POST['admission_status'] ?? 'Approved');
    $phone      = trim($_POST['phone'] ?? '');

    if (empty($fullName)) $errors[] = "Full name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $updateStmt = $pdo->prepare("UPDATE students SET full_name = ?, email = ?, course = ?, gender = ?, admission_status = ?, phone = ? WHERE student_id = ?");
            $updateStmt->execute([$fullName, $email, $course, $gender, $status, $phone, $studentId]);

            // Handle optional new profile photo replacement
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $maxFileSize = 5 * 1024 * 1024;
                $fileTmp  = $_FILES['profile_image']['tmp_name'];
                $fileName = $_FILES['profile_image']['name'];
                $fileSize = $_FILES['profile_image']['size'];
                $fileType = mime_content_type($fileTmp);

                if (in_array($fileType, ['image/jpeg', 'image/png']) && $fileSize <= $maxFileSize) {
                    $uploadDir = 'uploads/';
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $uniqueFileName = 'profile_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                    $targetPath = $uploadDir . $uniqueFileName;

                    if (move_uploaded_file($fileTmp, $targetPath)) {
                        // Unlink old profile image if exists
                        $oldPhotoStmt = $pdo->prepare("SELECT file_path FROM student_documents WHERE student_id = ? AND document_type = 'profile_image'");
                        $oldPhotoStmt->execute([$studentId]);
                        $oldPhoto = $oldPhotoStmt->fetch();
                        if ($oldPhoto && file_exists($oldPhoto['file_path'])) {
                            @unlink($oldPhoto['file_path']);
                        }

                        // Upsert new profile document record
                        $pdo->prepare("DELETE FROM student_documents WHERE student_id = ? AND document_type = 'profile_image'")->execute([$studentId]);
                        $docStmt = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, original_name, file_name, file_path, file_type, file_size) VALUES (?, 'profile_image', ?, ?, ?, ?, ?)");
                        $docStmt->execute([$studentId, $fileName, $uniqueFileName, $targetPath, $fileType, $fileSize]);
                    }
                }
            }

            $pdo->commit();
            header("Location: view_student.php?id={$studentId}&msg=" . urlencode("Student record updated successfully!"));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Update Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student Record | <?= htmlspecialchars($student['full_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <nav class="navbar">
        <div class="brand">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Student Record</span>
        </div>
        <div>
            <a href="view_student.php?id=<?= $studentId ?>" class="btn-admin btn-secondary"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-user-pen"></i>
                    <span>Modify Record for <?= htmlspecialchars($student['roll_number']) ?></span>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="form-card-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Roll Number (Read-Only)</label>
                        <input type="text" value="<?= htmlspecialchars($student['roll_number']) ?>" disabled style="background: #e2e8f0;">
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($student['full_name']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($student['email']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="course">Course *</label>
                        <select id="course" name="course" required>
                            <option value="B.E. Computer Science and Engineering" <?= $student['course'] === 'B.E. Computer Science and Engineering' ? 'selected' : '' ?>>B.E. Computer Science and Engineering</option>
                            <option value="B.Tech Information Technology" <?= $student['course'] === 'B.Tech Information Technology' ? 'selected' : '' ?>>B.Tech Information Technology</option>
                            <option value="B.E. Artificial Intelligence & Data Science" <?= $student['course'] === 'B.E. Artificial Intelligence & Data Science' ? 'selected' : '' ?>>B.E. Artificial Intelligence & Data Science</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="Male" <?= $student['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $student['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $student['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="admission_status">Admission Status</label>
                        <select id="admission_status" name="admission_status">
                            <option value="Approved" <?= $student['admission_status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Pending" <?= $student['admission_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Rejected" <?= $student['admission_status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="profile_image">Replace Profile Photo (Optional - JPG/PNG)</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/jpeg, image/png">
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="view_student.php?id=<?= $studentId ?>" class="btn-admin btn-secondary">Cancel</a>
                    <button type="submit" class="btn-admin btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
