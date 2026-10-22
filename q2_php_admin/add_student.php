<?php
/**
 * ============================================================================
 * Question 2: Add Student Record & Handle File Uploads with Metadata Storage
 * Handles photo & document uploads, validation (size, MIME type, no overwrite)
 * ============================================================================
 */
require_once __DIR__ . '/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize & Retrieve Form Inputs
    $name    = trim($_POST['name'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $course  = trim($_POST['course'] ?? '');

    // 2. Validate Inputs
    if (empty($name))    $errors[] = "Full Name is required.";
    if (empty($roll_no)) $errors[] = "Roll Number is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email Address is required.";
    }
    if (empty($course))  $errors[] = "Course selection is required.";

    // Check for duplicate Roll Number
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE roll_no = ?");
        $stmt->execute([$roll_no]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "A student with Roll Number '$roll_no' already exists.";
        }
    }

    // 3. Process Profile Photo Upload (if provided)
    $photoFileName = 'default_avatar.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['photo']['tmp_name'];
        $fileName      = $_FILES['photo']['name'];
        $fileSize      = $_FILES['photo']['size'];
        $fileExt       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // MIME Type verification using finfo
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileTmpPath);

        $allowedPhotoMimes = ['image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowedPhotoMimes)) {
            $errors[] = "Profile photo must be a valid JPG or PNG image.";
        } elseif ($fileSize > MAX_FILE_SIZE) {
            $errors[] = "Profile photo exceeds max size of 2MB.";
        } else {
            // Prevent overwriting: generate unique filename with timestamp & uniqid
            $photoFileName = 'photo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
            $destPath = UPLOAD_DIR . $photoFileName;

            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                $errors[] = "Failed to save profile photo to disk.";
            }
        }
    }

    // 4. If No Validation Errors, Insert Student Record
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $insertStudentSql = "INSERT INTO students (name, roll_no, email, course, photo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insertStudentSql);
            $stmt->execute([$name, $roll_no, $email, $course, $photoFileName]);
            $studentId = $pdo->lastInsertId();

            // 5. Process Verification Document (e.g. ID Proof / Certificate)
            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $docTmpPath = $_FILES['document']['tmp_name'];
                $docOrigName= $_FILES['document']['name'];
                $docSize    = $_FILES['document']['size'];
                $docExt     = strtolower(pathinfo($docOrigName, PATHINFO_EXTENSION));

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $docMime = $finfo->file($docTmpPath);

                $allowedDocMimes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!in_array($docMime, $allowedDocMimes)) {
                    $errors[] = "Document must be a JPG, PNG, or PDF file.";
                } elseif ($docSize > MAX_FILE_SIZE) {
                    $errors[] = "Document exceeds max size of 2MB.";
                } else {
                    // Prevent overwriting
                    $docSavedName = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $docExt;
                    $docDestPath = UPLOAD_DIR . $docSavedName;

                    if (move_uploaded_file($docTmpPath, $docDestPath)) {
                        // Store File Metadata in MySQL database linked to student record
                        $insertDocSql = "INSERT INTO student_documents 
                                        (student_id, file_name, file_path, file_type, file_size, upload_date) 
                                        VALUES (?, ?, ?, ?, ?, NOW())";
                        $docStmt = $pdo->prepare($insertDocSql);
                        $docStmt->execute([
                            $studentId,
                            $docOrigName,
                            'uploads/' . $docSavedName,
                            $docMime,
                            $docSize
                        ]);
                    }
                }
            }

            $pdo->commit();
            setFlash('success', "Student '{$name}' created successfully!");
            header("Location: index.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Student - Admin Panel</title>
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

  <div class="container" style="max-width: 750px;">
    
    <div class="page-header">
      <h2>Add New Student Record</h2>
      <a href="index.php" class="btn btn-secondary">← Back to List</a>
    </div>

    <!-- Error Summary Alerts -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul style="padding-left: 1.25rem;">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Add Student Form with File Enctype -->
    <div class="card">
      <form method="POST" action="add_student.php" enctype="multipart/form-data">
        
        <div class="form-grid">
          
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="e.g. Rahul Verma">
          </div>

          <div class="form-group">
            <label for="roll_no">Roll Number *</label>
            <input type="text" id="roll_no" name="roll_no" required value="<?= e($_POST['roll_no'] ?? '') ?>" placeholder="e.g. CS202610">
          </div>

          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="e.g. rahul@college.edu">
          </div>

          <div class="form-group">
            <label for="course">Academic Course *</label>
            <select id="course" name="course" required>
              <option value="">-- Select Course --</option>
              <option value="Computer Science" <?= (($_POST['course'] ?? '') === 'Computer Science') ? 'selected' : '' ?>>Computer Science</option>
              <option value="Information Technology" <?= (($_POST['course'] ?? '') === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
              <option value="Electronics & Comm." <?= (($_POST['course'] ?? '') === 'Electronics & Comm.') ? 'selected' : '' ?>>Electronics & Comm.</option>
              <option value="Mechanical Engg." <?= (($_POST['course'] ?? '') === 'Mechanical Engg.') ? 'selected' : '' ?>>Mechanical Engg.</option>
            </select>
          </div>

          <!-- Profile Image Upload Handling -->
          <div class="form-group">
            <label for="photo">Profile Photo (Image)</label>
            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png">
            <span class="file-help">JPG or PNG, max 2MB. Unique filename generated automatically.</span>
          </div>

          <!-- Document Upload Handling (Metadata stored in DB) -->
          <div class="form-group">
            <label for="document">Verification Document (ID / Certificate)</label>
            <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">
            <span class="file-help">PDF, JPG, or PNG. Metadata logged into database.</span>
          </div>

        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
          <button type="submit" class="btn btn-primary" style="flex: 1;">Save Student Record</button>
          <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>

      </form>
    </div>

  </div>

</body>
</html>
