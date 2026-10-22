<?php
/**
 * ============================================================================
 * Question 2: Edit Student Details & Update/Upload Profile Documents
 * ============================================================================
 */
require_once __DIR__ . '/config.php';

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($studentId <= 0) {
    setFlash('danger', 'Invalid student ID.');
    header("Location: index.php");
    exit;
}

// Fetch current student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', 'Student record not found.');
    header("Location: index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $course  = trim($_POST['course'] ?? '');

    if (empty($name))    $errors[] = "Full Name is required.";
    if (empty($roll_no)) $errors[] = "Roll Number is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email Address is required.";
    }
    if (empty($course))  $errors[] = "Course selection is required.";

    // Check roll number uniqueness for other students
    $chkStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE roll_no = ? AND id != ?");
    $chkStmt->execute([$roll_no, $studentId]);
    if ($chkStmt->fetchColumn() > 0) {
        $errors[] = "Another student is already using Roll Number '$roll_no'.";
    }

    $photoFileName = $student['photo'];

    // Handle new photo upload if provided
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['photo']['tmp_name'];
        $fileName      = $_FILES['photo']['name'];
        $fileSize      = $_FILES['photo']['size'];
        $fileExt       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($fileTmpPath);

        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            $errors[] = "Profile photo must be a JPG or PNG image.";
        } elseif ($fileSize > MAX_FILE_SIZE) {
            $errors[] = "Profile photo exceeds 2MB limit.";
        } else {
            // Delete old photo if it is custom
            if ($photoFileName && $photoFileName !== 'default_avatar.png' && file_exists(UPLOAD_DIR . $photoFileName)) {
                @unlink(UPLOAD_DIR . $photoFileName);
            }
            $photoFileName = 'photo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
            move_uploaded_file($fileTmpPath, UPLOAD_DIR . $photoFileName);
        }
    }

    // Handle new additional document upload
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Update student core details
            $updateSql = "UPDATE students SET name = ?, roll_no = ?, email = ?, course = ?, photo = ? WHERE id = ?";
            $upStmt = $pdo->prepare($updateSql);
            $upStmt->execute([$name, $roll_no, $email, $course, $photoFileName, $studentId]);

            // Save new document if uploaded
            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $docTmpPath = $_FILES['document']['tmp_name'];
                $docOrigName= $_FILES['document']['name'];
                $docSize    = $_FILES['document']['size'];
                $docExt     = strtolower(pathinfo($docOrigName, PATHINFO_EXTENSION));

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $docMime = $finfo->file($docTmpPath);

                if (in_array($docMime, ['image/jpeg', 'image/png', 'application/pdf']) && $docSize <= MAX_FILE_SIZE) {
                    $docSavedName = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $docExt;
                    if (move_uploaded_file($docTmpPath, UPLOAD_DIR . $docSavedName)) {
                        $docSql = "INSERT INTO student_documents (student_id, file_name, file_path, file_type, file_size, upload_date) 
                                   VALUES (?, ?, ?, ?, ?, NOW())";
                        $pdo->prepare($docSql)->execute([$studentId, $docOrigName, 'uploads/' . $docSavedName, $docMime, $docSize]);
                    }
                }
            }

            $pdo->commit();
            setFlash('success', "Student '{$name}' updated successfully!");
            header("Location: view_student.php?id=" . $studentId);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Update failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Student - Admin Panel</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <nav class="navbar">
    <div class="nav-brand">🏛 College Admin Portal</div>
    <div class="nav-links">
      <a href="index.php">Dashboard</a>
      <a href="add_student.php">+ Add Student</a>
    </div>
  </nav>

  <div class="container" style="max-width: 750px;">
    
    <div class="page-header">
      <h2>Edit Student Record</h2>
      <a href="view_student.php?id=<?= $student['id'] ?>" class="btn btn-secondary">← Back to Profile</a>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul style="padding-left: 1.25rem;">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="edit_student.php?id=<?= $student['id'] ?>" enctype="multipart/form-data">
        
        <div class="form-grid">
          
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" required value="<?= e($_POST['name'] ?? $student['name']) ?>">
          </div>

          <div class="form-group">
            <label for="roll_no">Roll Number *</label>
            <input type="text" id="roll_no" name="roll_no" required value="<?= e($_POST['roll_no'] ?? $student['roll_no']) ?>">
          </div>

          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? $student['email']) ?>">
          </div>

          <div class="form-group">
            <label for="course">Academic Course *</label>
            <?php $selCourse = $_POST['course'] ?? $student['course']; ?>
            <select id="course" name="course" required>
              <option value="Computer Science" <?= ($selCourse === 'Computer Science') ? 'selected' : '' ?>>Computer Science</option>
              <option value="Information Technology" <?= ($selCourse === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
              <option value="Electronics & Comm." <?= ($selCourse === 'Electronics & Comm.') ? 'selected' : '' ?>>Electronics & Comm.</option>
              <option value="Mechanical Engg." <?= ($selCourse === 'Mechanical Engg.') ? 'selected' : '' ?>>Mechanical Engg.</option>
            </select>
          </div>

          <div class="form-group">
            <label for="photo">Change Profile Photo</label>
            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png">
            <span class="file-help">Leave empty to keep current photo.</span>
          </div>

          <div class="form-group">
            <label for="document">Upload Another Verification Document</label>
            <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">
            <span class="file-help">Attach additional certificate or ID proof.</span>
          </div>

        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">Update Student</button>
          <a href="view_student.php?id=<?= $student['id'] ?>" class="btn btn-secondary">Cancel</a>
        </div>

      </form>
    </div>

  </div>

</body>
</html>
