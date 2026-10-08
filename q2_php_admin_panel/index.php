<?php
/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 - Web Technology | Assignment II - Q2
 * 
 * PHP Admin Panel - Student Records Dashboard (CRUD Operations)
 */

require_once 'config/db.php';

$pdo = getDBConnection();

// Handle Search Query if provided
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT s.*, d.file_path AS profile_path 
                           FROM students s 
                           LEFT JOIN student_documents d ON s.student_id = d.student_id AND d.document_type = 'profile_image'
                           WHERE s.full_name LIKE ? OR s.roll_number LIKE ? OR s.email LIKE ? OR s.course LIKE ?
                           ORDER BY s.student_id DESC");
    $searchTerm = "%{$search}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $pdo->query("SELECT s.*, d.file_path AS profile_path 
                         FROM students s 
                         LEFT JOIN student_documents d ON s.student_id = d.student_id AND d.document_type = 'profile_image'
                         ORDER BY s.student_id DESC");
}

$students = $stmt->fetchAll();

// Count total metrics
$totalStudents = count($students);
$approvedCount = count(array_filter($students, fn($s) => $s['admission_status'] === 'Approved'));
$pendingCount = count(array_filter($students, fn($s) => $s['admission_status'] === 'Pending'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPRIET Admin - Student Records (Q2 PHP MySQL CRUD)</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <nav class="navbar">
        <div class="brand">
            <i class="fa-solid fa-user-shield"></i>
            <span>KPRIET Admin Panel</span>
        </div>
        <div class="meta-info">
            <span>U21CS501 Web Technology • Question 2 PHP CRUD & File Handling</span>
        </div>
    </nav>

    <div class="container">
        <!-- Top Flash Alert Message -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= htmlspecialchars($_GET['msg']) ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <h3><?= $totalStudents ?></h3>
                    <p>Total Records</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #f0fdf4; color: #10b981;"><i class="fa-solid fa-user-check"></i></div>
                <div class="stat-info">
                    <h3><?= $approvedCount ?></h3>
                    <p>Approved Admissions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-info">
                    <h3><?= $pendingCount ?></h3>
                    <p>Pending Verifications</p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Data Table -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Student Profile Directory</span>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <form method="GET" class="search-box">
                        <input type="text" name="search" placeholder="Search by name, roll, email..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn-admin btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                    <a href="add_student.php" class="btn-admin btn-primary">
                        <i class="fa-solid fa-plus"></i> Add Student Record
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Roll Number</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    No student records found. Click <strong>"Add Student Record"</strong> to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($student['profile_path']) && file_exists($student['profile_path'])): ?>
                                            <img src="<?= htmlspecialchars($student['profile_path']) ?>" alt="Photo" class="avatar-thumb">
                                        <?php else: ?>
                                            <div class="avatar-thumb" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b; font-weight: bold;">
                                                <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($student['roll_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($student['full_name']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= htmlspecialchars($student['course']) ?></td>
                                    <td>
                                        <span class="status-badge <?= $student['admission_status'] === 'Approved' ? 'status-approved' : 'status-pending' ?>">
                                            <?= htmlspecialchars($student['admission_status']) ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="view_student.php?id=<?= $student['student_id'] ?>" class="btn-admin btn-info" title="View Profile & Files">
                                            <i class="fa-solid fa-eye"></i> View
                                        </a>
                                        <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn-admin btn-secondary" title="Edit Student">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <a href="delete_student.php?id=<?= $student['student_id'] ?>" 
                                           class="btn-admin btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete student <?= htmlspecialchars($student['full_name']) ?> and all uploaded files?');"
                                           title="Delete Student">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
