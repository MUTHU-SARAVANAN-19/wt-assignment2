<?php
/**
 * ============================================================================
 * Question 2: View Student Profile & Display Uploaded Files
 * Renders student details and links to all uploaded documents with metadata
 * ============================================================================
 */
require_once __DIR__ . '/config.php';

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($studentId <= 0) {
    setFlash('danger', 'Invalid student ID.');
    header("Location: index.php");
    exit;
}

// 1. Fetch Student Details
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', 'Student not found.');
    header("Location: index.php");
    exit;
}

// 2. Fetch Associated Document Metadata
$docStmt = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ? ORDER BY upload_date DESC");
$docStmt->execute([$studentId]);
$documents = $docStmt->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Student Details - Admin Panel</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-brand">🏛 College Admin Portal</div>
    <div class="nav-links">
      <a href="index.php">Dashboard</a>
      <a href="add_student.php">+ Add Student</a>
    </div>
  </nav>

  <div class="container">
    
    <div class="page-header">
      <h2>Student Profile & Documents</h2>
      <div style="display: flex; gap: 0.5rem;">
        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-primary">✏ Edit</a>
        <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <!-- Student Profile Overview Card -->
    <div class="card">
      <div class="profile-card">
        <?php 
          $photoPath = !empty($student['photo']) && file_exists(UPLOAD_DIR . $student['photo'])
                      ? 'uploads/' . e($student['photo'])
                      : 'uploads/default_avatar.png';
        ?>
        <img src="<?= $photoPath ?>" alt="Student Photo" class="profile-large-avatar">
        <div class="profile-info">
          <h3><?= e($student['name']) ?></h3>
          <p><strong>Roll Number:</strong> <?= e($student['roll_no']) ?></p>
          <p><strong>Email Address:</strong> <?= e($student['email']) ?></p>
          <p><strong>Academic Course:</strong> <span class="badge"><?= e($student['course']) ?></span></p>
          <p><strong>Record Created:</strong> <?= e($student['created_at']) ?></p>
        </div>
      </div>

      <!-- Uploaded Verification Documents Section -->
      <h3 style="margin-top: 1.5rem; margin-bottom: 1rem; font-size: 1.15rem;">
        📁 Uploaded Verification Documents (<?= count($documents) ?>)
      </h3>

      <?php if (empty($documents)): ?>
        <p style="color: var(--text-muted); padding: 1rem; background-color: #f8fafc; border-radius: var(--radius);">
          No verification documents uploaded for this student yet.
        </p>
      <?php else: ?>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Document Name</th>
                <th>File Type (MIME)</th>
                <th>File Size</th>
                <th>Uploaded At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($documents as $doc): ?>
                <tr>
                  <td>
                    <strong>📄 <?= e($doc['file_name']) ?></strong>
                  </td>
                  <td><span class="badge" style="background:#f1f5f9; color:#475569;"><?= e($doc['file_type']) ?></span></td>
                  <td><?= round($doc['file_size'] / 1024, 1) ?> KB</td>
                  <td><?= e($doc['upload_date']) ?></td>
                  <td>
                    <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                      👁 View / Download
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>

  </div>

</body>
</html>
