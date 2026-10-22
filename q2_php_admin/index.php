<?php
/**
 * ============================================================================
 * Question 2: Admin Dashboard (View All Students & CRUD Gateway)
 * Displays student records with document counts and management options
 * ============================================================================
 */
require_once __DIR__ . '/config.php';

// Fetch all students along with count of their uploaded documents
$sql = "SELECT s.*, COUNT(d.id) AS doc_count 
        FROM students s 
        LEFT JOIN student_documents d ON s.id = d.student_id 
        GROUP BY s.id 
        ORDER BY s.id DESC";

try {
    $stmt = $pdo->query($sql);
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query Error: " . htmlspecialchars($e->getMessage()));
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Student Records Management</title>
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
    
    <!-- Flash Notifications (Success / Error) -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
      </div>
    <?php endif; ?>

    <div class="page-header">
      <div>
        <h2>Student Records Directory</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Manage enrolled student profiles, photos, and verification documents</p>
      </div>
      <a href="add_student.php" class="btn btn-primary">+ Add New Student</a>
    </div>

    <!-- Student Records Table -->
    <div class="card">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Photo</th>
              <th>Roll Number</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Course</th>
              <th>Documents</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($students)): ?>
              <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                  No student records found. Click "+ Add New Student" to get started.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($students as $row): ?>
                <tr>
                  <td>
                    <?php 
                      $photoSrc = !empty($row['photo']) && file_exists(UPLOAD_DIR . $row['photo'])
                                  ? 'uploads/' . e($row['photo']) 
                                  : 'uploads/default_avatar.png';
                    ?>
                    <img src="<?= $photoSrc ?>" alt="Avatar" class="avatar-thumbnail">
                  </td>
                  <td><strong><?= e($row['roll_no']) ?></strong></td>
                  <td><?= e($row['name']) ?></td>
                  <td><?= e($row['email']) ?></td>
                  <td><span class="badge"><?= e($row['course']) ?></span></td>
                  <td>
                    <span class="badge" style="background-color: #f1f5f9; color: #475569;">
                      📎 <?= (int)$row['doc_count'] ?> files
                    </span>
                  </td>
                  <td style="white-space: nowrap;">
                    <a href="view_student.php?id=<?= (int)$row['id'] ?>" class="btn btn-secondary btn-sm">👁 View</a>
                    <a href="edit_student.php?id=<?= (int)$row['id'] ?>" class="btn btn-secondary btn-sm">✏ Edit</a>
                    <a href="delete_student.php?id=<?= (int)$row['id'] ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to delete student <?= e($row['name']) ?> and all associated documents?');">
                       🗑 Delete
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
