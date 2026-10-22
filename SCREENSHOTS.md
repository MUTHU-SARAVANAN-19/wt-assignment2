# Web Technology Assignment 2 - Output Screenshots

This document aggregates browser output screenshots for all three questions in the assignment.
The raw screenshot image files are located in the [`screenshots/`](screenshots/) directory.

---

## Question 1: Student Admission Registration Form (HTML, CSS, JS)

### 1. Initial Registration Form State
Clean, responsive layout styled with modern CSS variables, card container, styled inputs, and password strength requirements checklist.

![Question 1: Registration Form](screenshots/01_q1_registration_form.png)

---

### 2. Client-Side Validation & Dynamic Password Strength
Demonstrates real-time JavaScript validation: required fields check, email format regex validation, file type/size validation, and dynamic password strength evaluation (Medium strength bar with satisfied checklist items).

![Question 1: Client-Side Validation](screenshots/02_q1_client_validation.png)

---

## Question 2: College Admin Panel (PHP + MySQL CRUD & File Uploads)

### 3. Admin Dashboard & Student Directory
Displays student records fetched from MySQL using PDO, profile photos, course badges, uploaded document count, and CRUD action triggers (View, Edit, Delete).

![Question 2: Admin Dashboard](screenshots/03_q2_admin_dashboard.png)

---

### 4. Add Student Form with File & Document Upload
Allows administrators to add student records, upload a profile photo (JPG/PNG), and attach verification documents (PDF/ID proof) with automatic unique file renaming to prevent overwriting.

![Question 2: Add Student Form](screenshots/04_q2_add_student.png)

---

### 5. View Student Details & Uploaded Documents Metadata
Displays complete student profile details and an interactive table of all attached documents with their metadata (File Name, MIME Type, File Size in KB, and Upload Date) along with view/download links.

![Question 2: View Student Profile](screenshots/05_q2_view_student.png)

---

## Question 3: Front-End Student Portal (Java Servlets, JSP & JDBC)

### 6. Portal Secure Login (Session Authentication)
Secure gateway requiring authentication against MySQL `users` table before establishing an `HttpSession`.

![Question 3: Servlet Login](screenshots/06_q3_servlet_login.png)

---

### 7. Student Profiles Dynamic Portal (Image Streaming & Looping)
Displays student cards inside `portal.jsp` using dynamic Java scriptlet looping and conditional logic. Student profile photos are streamed directly through `FileStreamServlet` (`<img src="streamFile?file=...">`), and verification documents are listed with direct streaming links.

![Question 3: Student Profiles Dynamic Portal](screenshots/07_q3_student_portal.png)
