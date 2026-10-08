<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 - Web Technology | Assignment II - Q2
 * 
 * Add Student Record & Secure File Upload Processing
 */

require_once 'config/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();

    // Sanitize Form Inputs
    $rollNumber = trim($_POST['roll_number'] ?? '');
    $fullName   = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $course     = trim($_POST['course'] ?? '');
    $gender     = trim($_POST['gender'] ?? 'Male');
    $dob        = !empty($_POST['dob']) ? $_POST['dob'] : null;
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');

    // Form Validations
    if (empty($rollNumber)) $errors[] = "Roll number is required.";
    if (empty($fullName))   $errors[] = "Full name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($course))     $errors[] = "Course selection is required.";

    // Check duplicate roll number or email
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE roll_number = ? OR email = ?");
    $checkStmt->execute([$rollNumber, $email]);
    if ($checkStmt->fetchColumn() > 0) {
        $errors[] = "Student with this Roll Number or Email already exists in MySQL database.";
    }

    // File Upload Validation Rules
    $maxFileSize = 5 * 1024 * 1024; // 5 MB Limit
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $allowedDocTypes   = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'];

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Process Profile Image Upload
    $profileUploaded = false;
    $profileFileData = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileSize = $_FILES['profile_image']['size'];
        $fileType = mime_content_type($fileTmp);

        if (!in_array($fileType, $allowedImageTypes)) {
            $errors[] = "Profile Image must be a valid JPG or PNG image.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "Profile Image file size exceeds maximum 5MB limit.";
        } else {
            // Prevent overwriting using unique hash identifier
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = 'profile_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            $targetPath = $uploadDir . $uniqueFileName;
            $profileFileData = [
                'original_name' => $fileName,
                'file_name'     => $uniqueFileName,
                'file_path'     => $targetPath,
                'file_type'     => $fileType,
                'file_size'     => $fileSize,
                'tmp_name'      => $fileTmp
            ];
        }
    } else {
        $errors[] = "Profile photo upload is required.";
    }

    // Process Document Upload (ID Proof)
    $docFileData = null;
    if (isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['id_proof']['tmp_name'];
        $fileName = $_FILES['id_proof']['name'];
        $fileSize = $_FILES['id_proof']['size'];
        $fileType = mime_content_type($fileTmp);

        if (!in_array($fileType, $allowedDocTypes) && !preg_match('/\.(pdf|docx)$/i', $fileName)) {
            $errors[] = "ID Document must be a PDF or DOCX file.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "ID Document file size exceeds maximum 5MB limit.";
        } else {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = 'doc_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            $targetPath = $uploadDir . $uniqueFileName;
            $docFileData = [
                'original_name' => $fileName,
                'file_name'     => $uniqueFileName,
                'file_path'     => $targetPath,
                'file_type'     => $fileType,
                'file_size'     => $fileSize,
                'tmp_name'      => $fileTmp
            ];
        }
    } else {
        $errors[] = "ID proof document upload is required.";
    }

    // If no errors, insert into MySQL database & move files
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Insert Student Core Details
            $stmt = $pdo->prepare("INSERT INTO students (roll_number, full_name, email, course, gender, dob, phone, address) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$rollNumber, $fullName, $email, $course, $gender, $dob, $phone, $address]);
            $studentId = $pdo->lastInsertId();

            // 2. Move Profile File and Store Metadata in student_documents table
            if ($profileFileData && move_uploaded_file($profileFileData['tmp_name'], $profileFileData['file_path'])) {
                $docStmt = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, original_name, file_name, file_path, file_type, file_size) 
                                         VALUES (?, 'profile_image', ?, ?, ?, ?, ?)");
                $docStmt->execute([
                    $studentId,
                    $profileFileData['original_name'],
                    $profileFileData['file_name'],
                    $profileFileData['file_path'],
                    $profileFileData['file_type'],
                    $profileFileData['file_size']
                ]);
            }

            // 3. Move ID Document File and Store Metadata
            if ($docFileData && move_uploaded_file($docFileData['tmp_name'], $docFileData['file_path'])) {
                $docStmt = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, original_name, file_name, file_path, file_type, file_size) 
                                         VALUES (?, 'id_proof', ?, ?, ?, ?, ?)");
                $docStmt->execute([
                    $studentId,
                    $docFileData['original_name'],
                    $docFileData['file_name'],
                    $docFileData['file_path'],
                    $docFileData['file_type'],
                    $docFileData['file_size']
                ]);
            }

            $pdo->commit();

            header("Location: index.php?msg=" . urlencode("Student record and file metadata inserted successfully!"));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Database Transaction Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student Record | Q2 PHP MySQL</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <nav class="navbar">
        <div class="brand">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add Student - Admin Panel</span>
        </div>
        <div>
            <a href="index.php" class="btn-admin btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <div>
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Submission Errors:</strong>
                    <ul style="margin-left: 1.25rem; margin-top: 0.4rem;">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Register New Student & Upload Credentials</span>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="form-card-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="roll_number">Roll Number *</label>
                        <input type="text" id="roll_number" name="roll_number" placeholder="7377211CS101" required value="<?= htmlspecialchars($_POST['roll_number'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Aravind Swamy" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="aravind@kpriet.ac.in" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="course">Course *</label>
                        <select id="course" name="course" required>
                            <option value="">Select Degree Course</option>
                            <option value="B.E. Computer Science and Engineering">B.E. Computer Science and Engineering</option>
                            <option value="B.Tech Information Technology">B.Tech Information Technology</option>
                            <option value="B.E. Artificial Intelligence & Data Science">B.E. Artificial Intelligence & Data Science</option>
                            <option value="B.E. Cyber Security">B.E. Cyber Security</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="9876543210" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>

                    <!-- File Upload Inputs -->
                    <div class="form-group full">
                        <label for="profile_image">Upload Profile Photo (JPG/PNG, Max 5MB) *</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/jpeg, image/png" required>
                    </div>

                    <div class="form-group full">
                        <label for="id_proof">Upload ID Proof Document (PDF/DOCX, Max 5MB) *</label>
                        <input type="file" id="id_proof" name="id_proof" accept="application/pdf, .docx" required>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="index.php" class="btn-admin btn-secondary">Cancel</a>
                    <button type="submit" class="btn-admin btn-primary"><i class="fa-solid fa-save"></i> Save Student Record</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
