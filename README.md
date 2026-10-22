# Web Technology Assignment 2: Student Management & Admission System

This repository contains a lightweight, modular, and complete implementation of **Web Technology Assignment 2**, covering:
1. **Question 1 (HTML5, CSS3, JavaScript)**: Responsive Student Admission Registration Portal with client-side form validation and real-time password strength meter.
2. **Question 2 (PHP & MySQL)**: College Admin Panel managing student records (CRUD operations), secure file uploads (photos, ID proofs, certificates), metadata logging in MySQL, and prevention of file overwriting.
3. **Question 3 (Java Servlets, JSP, JDBC)**: Authenticated Front-End Portal using session management, JDBC database connectivity, dynamic looping/conditional rendering, and streaming of stored images/documents via a dedicated Servlet.

---

## 📁 Project Directory Structure

```text
WT ASS2 NEW/
│
├── db/
│   └── schema.sql                  # MySQL script creating student_portal DB, tables & seed data
│
├── q1_registration/                # Question 1: Admission Registration Form
│   ├── index.html                  # HTML5 form with styled inputs, file upload & password meter
│   ├── style.css                   # Responsive CSS layout, variables, transitions & error styling
│   └── script.js                   # Client-side validation logic & password strength checker
│
├── q2_php_admin/                   # Question 2: College Admin Panel (PHP + MySQL)
│   ├── config.php                  # PDO database connection, constants & flash message helpers
│   ├── index.php                   # Admin dashboard listing students with document counts
│   ├── add_student.php             # Add student form + file validation + unique rename logic
│   ├── edit_student.php            # Edit student details and update/upload documents
│   ├── view_student.php            # View student details & display uploaded files metadata
│   ├── delete_student.php          # Delete student record & clean up associated files from disk
│   ├── style.css                   # Admin panel styling
│   └── uploads/                    # Physical storage directory for photos & verification files
│
├── q3_servlet_portal/              # Question 3: Java Servlets & JSP Portal
│   ├── src/com/portal/
│   │   ├── DBConnection.java       # JDBC connection manager
│   │   ├── Student.java            # Student & Document JavaBean models
│   │   ├── LoginServlet.java       # Authenticates credentials & creates HttpSession
│   │   ├── LogoutServlet.java      # Destroys session
│   │   ├── StudentListServlet.java # Queries MySQL via JDBC, forwards data to portal.jsp
│   │   └── FileStreamServlet.java  # Streams image/doc bytes to <img> tags & download links
│   └── web/
│       ├── WEB-INF/
│       │   ├── web.xml             # Deployment descriptor with servlet mappings
│       │   ├── classes/com/portal/ # Compiled .class bytecode
│       │   └── lib/                # mysql-connector-j-8.0.33.jar
│       ├── login.jsp               # Session authentication login page
│       ├── portal.jsp              # Dynamic student cards with <img> pointing to servlet
│       ├── style.css               # Portal styles
│       └── uploads/                # Webapp copy of avatar images & documents
│
├── screenshots/                    # High-resolution output screenshots
│   ├── 01_q1_registration_form.png
│   ├── 02_q1_client_validation.png
│   ├── 03_q2_admin_dashboard.png
│   ├── 04_q2_add_student.png
│   ├── 05_q2_view_student.png
│   ├── 06_q3_servlet_login.png
│   └── 07_q3_student_portal.png
│
├── SCREENSHOTS.md                  # Screenshots showcase document
└── README.md                       # Complete documentation & execution guide
```

---

## 🛠 Prerequisites

1. **XAMPP Control Panel** (Installed at `C:\xampp`) with:
   - **Apache** (Web Server)
   - **MySQL** (Database Server)
   - **Tomcat** (Java Servlet / JSP container)
2. **Java Development Kit (JDK 8 or higher)** (`javac` and `java` commands)
3. Any modern web browser (Google Chrome, Microsoft Edge, Firefox)

---

## 🚀 Step-by-Step Guidance to Run the Project

### Step 1: Database Setup (MySQL)
1. Open the **XAMPP Control Panel** and click **Start** next to **MySQL** and **Apache**.
2. Open your browser and navigate to:
   ```
   http://localhost/phpmyadmin
   ```
3. Click on the **Import** tab.
4. Click **Choose File**, select `db/schema.sql` from this project folder, and click **Go** (or execute the contents in the SQL tab).
5. This creates:
   - Database: `student_portal`
   - Table `users`: With default admin credentials (`admin` / `password123`)
   - Table `students`: Seeded with sample student profiles
   - Table `student_documents`: Linked via Foreign Key with metadata (filename, path, MIME type, file size, upload timestamp).

---

### Step 2: Running Question 1 (Student Admission Portal Form)
1. Open `q1_registration/index.html` directly in any web browser by double clicking it, or run a local server:
   ```powershell
   # Option A: Double click q1_registration/index.html
   # Option B: Run Python HTTP server
   cd "c:\Users\Muthu Saravanan\OneDrive\Desktop\WT ASS2 NEW"
   python -m http.server 8000
   ```
2. Navigate to:
   ```
   http://localhost:8000/q1_registration/index.html
   ```
3. Test validation features:
   - Submit empty fields to view custom red error messages.
   - Enter an invalid email to trigger pattern validation.
   - Type in the password field to watch the real-time strength meter (Weak / Medium / Strong) and the 5 criteria checkmarks update dynamically.
   - Upload non-image/non-PDF files or files > 2MB to observe file validation.

---

### Step 3: Running Question 2 (PHP College Admin Panel)
1. Copy the `q2_php_admin` directory to your XAMPP Apache `htdocs` directory:
   ```powershell
   Copy-Item -Recurse -Force "q2_php_admin" "C:\xampp\htdocs\student_admin"
   ```
2. Ensure **Apache** and **MySQL** are running in XAMPP.
3. Open your browser and visit:
   ```
   http://localhost/student_admin/index.php
   ```
4. Perform CRUD operations:
   - **Add**: Click `+ Add Student`, fill details, select photo & document, and click Save.
   - **View**: Click `👁 View` to inspect the student profile and the uploaded documents table.
   - **Edit**: Click `✏ Edit` to update student info or upload replacement files.
   - **Delete**: Click `🗑 Delete` to delete the student and unlink physical files from `uploads/`.

---

### Step 4: Running Question 3 (Java Servlets & JSP Front-End Portal)
1. Deploy the `q3_servlet_portal/web` folder to Tomcat `webapps`:
   ```powershell
   # Create student_portal webapp folder in Tomcat
   New-Item -ItemType Directory -Force "C:\xampp\tomcat\webapps\student_portal"
   Copy-Item -Recurse -Force "q3_servlet_portal\web\*" "C:\xampp\tomcat\webapps\student_portal\"
   ```
2. Start Tomcat from XAMPP Control Panel or via command line:
   ```powershell
   & "C:\xampp\tomcat\bin\startup.bat"
   ```
3. Open your browser and navigate to:
   ```
   http://localhost:8080/student_portal/login
   ```
4. Log in using default credentials:
   - **Username**: `admin`
   - **Password**: `password123`
5. Features to observe:
   - **Session Authentication**: Direct access to `/portal.jsp` or `/students` without login is blocked.
   - **Streaming Servlet**: The profile photos inside `<img src="streamFile?file=...">` are served on-the-fly by `FileStreamServlet`.
   - **JSP Looping & Logic**: Dynamic cards iterate through students and attached verification documents.
   - **Sign Out**: Click `Sign Out` to invalidate the session and return to login.

---

## 📝 Code Explanation

### Question 1: HTML5, CSS3, & Client-Side JavaScript
- **HTML Structure (`index.html`)**: Features semantic `<form novalidate>`, accessible `<label>` associations, dedicated `<small class="error-msg">` elements, and a dynamic checklist for password criteria.
- **Styling (`style.css`)**: Built with CSS custom properties (`--primary-color`, `--shadow-lg`), CSS Flexbox, and CSS Grid. Highlighting transitions switch input border colors smoothly between invalid (`#ef4444`) and valid (`#10b981`).
- **JavaScript Validation (`script.js`)**:
  - `validateEmail()`: Employs regex pattern `^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$` to verify formatting.
  - `checkPasswordStrength()`: Calculates a weighted score across 5 criteria (length >= 8, uppercase, lowercase, numbers, symbols) and dynamically adjusts the progress bar width and color (red, orange, green).
  - `validateFileUpload()`: Checks file extension against `['jpg', 'jpeg', 'png', 'pdf']` and enforces the 2MB size ceiling before upload.

### Question 2: PHP & MySQL CRUD with Secure File Uploads
- **Database Connection (`config.php`)**: Implements `PDO` with `ATTR_ERRMODE => ERRMODE_EXCEPTION` and emulated prepares disabled to protect against SQL Injection.
- **CRUD Workflow (`index.php`, `add_student.php`, `edit_student.php`, `delete_student.php`)**: Full lifecycle management with interactive UI and flash messaging.
- **Secure File Handling**:
  - **MIME Inspection**: Uses PHP `finfo(FILEINFO_MIME_TYPE)` to verify raw binary file headers rather than trusting client file extensions alone.
  - **Collision Prevention**: Generates randomized, timestamped file names (`'photo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext`) so existing files are never overwritten.
  - **Metadata Logging**: Inserts file path, MIME type, file size, and upload timestamp into the `student_documents` table linked to the parent student ID.
  - **Disk Cleanup**: When a student is deleted, `delete_student.php` unlinks physical files from `uploads/` before removing the database row.

### Question 3: Java Servlets, JSP, JDBC, & Streaming
- **JDBC Connection (`DBConnection.java`)**: Manages `DriverManager.getConnection()` using MySQL Connector/J with connection parameters.
- **Session Security (`LoginServlet.java` & `LogoutServlet.java`)**: Validates credentials from `users` table, provisions `request.getSession(true)`, binds `session.setAttribute("user", username)`, and enforces a 30-minute idle session timeout.
- **Protected Retrieval (`StudentListServlet.java`)**: Guards against unauthorized access (`session == null`), queries MySQL for student and document records, binds the resulting `List<Student>` to the request scope, and forwards to `portal.jsp`.
- **Dynamic Presentation (`portal.jsp`)**: Uses Java scriptlets for conditional logic (checking if document list is empty) and looping logic (iterating through student profiles).
- **Binary Streaming (`FileStreamServlet.java`)**:
  - Receives `?file=...` parameter.
  - Strips path traversal sequences with `new File(filename).getName()`.
  - Determines Content-Type dynamically (`image/png`, `image/jpeg`, `application/pdf`).
  - Reads bytes into a 4KB buffer (`byte[] buffer = new byte[4096]`) and writes directly to `response.getOutputStream()`.

---

## 📌 Brief Conclusion

This project successfully integrates front-end client-side validation, server-side PHP data and file persistence, and enterprise Java Servlet/JSP session-secured streaming into a cohesive academic student portal. By enforcing rigorous input validation, metadata cataloging in MySQL, and direct binary response streaming, the application demonstrates robust full-stack web engineering principles. The solution adheres strictly to modular separation of concerns while keeping the codebase lightweight, clear, and easy to maintain.

---

## 🌐 GitHub Repository Setup

To push this project to your GitHub account:
```bash
git init
git add .
git commit -m "Complete Web Technology Assignment 2: Registration, PHP Admin, and Java Servlet Portal"
git branch -M main
git remote add origin https://github.com/<YOUR_USERNAME>/<YOUR_REPOSITORY_NAME>.git
git push -u origin main
```
