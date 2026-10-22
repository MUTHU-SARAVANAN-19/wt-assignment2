<?php
/**
 * ============================================================================
 * Question 2: Delete Student Record & Cleanup Stored Files from Disk
 * Removes linked document metadata and unlinks physical files
 * ============================================================================
 */
require_once __DIR__ . '/config.php';

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($studentId > 0) {
    try {
        // 1. Fetch Student Photo filename
        $stmt = $pdo->prepare("SELECT photo, name FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();

        if ($student) {
            $studentName = $student['name'];

            // 2. Fetch all linked document file paths
            $docStmt = $pdo->prepare("SELECT file_path FROM student_documents WHERE student_id = ?");
            $docStmt->execute([$studentId]);
            $documents = $docStmt->fetchAll();

            // 3. Delete physical files from uploads directory
            foreach ($documents as $doc) {
                // file_path is like 'uploads/doc_xxx.pdf'
                $absPath = __DIR__ . '/' . $doc['file_path'];
                if (file_exists($absPath) && is_file($absPath)) {
                    @unlink($absPath);
                }
            }

            // Delete custom photo if not default
            if (!empty($student['photo']) && $student['photo'] !== 'default_avatar.png') {
                $photoPath = UPLOAD_DIR . $student['photo'];
                if (file_exists($photoPath) && is_file($photoPath)) {
                    @unlink($photoPath);
                }
            }

            // 4. Delete student from database (student_documents automatically deleted via ON DELETE CASCADE)
            $delStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $delStmt->execute([$studentId]);

            setFlash('success', "Student '{$studentName}' and associated documents were successfully deleted.");
        } else {
            setFlash('danger', "Student record not found.");
        }
    } catch (PDOException $e) {
        setFlash('danger', "Failed to delete student: " . $e->getMessage());
    }
} else {
    setFlash('danger', "Invalid student ID.");
}

// Redirect back to admin dashboard
header("Location: index.php");
exit;
