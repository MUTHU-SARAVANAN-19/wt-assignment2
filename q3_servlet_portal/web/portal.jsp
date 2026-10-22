<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.util.List" %>
<%@ page import="com.portal.Student" %>
<%
    // -------------------------------------------------------------------------
    // Session Guard: Prevent unauthorized direct access to this JSP page
    // -------------------------------------------------------------------------
    String currentUser = (String) session.getAttribute("user");
    if (currentUser == null) {
        response.sendRedirect(request.getContextPath() + "/login?error=unauthorized");
        return;
    }

    // Retrieve the student list populated by StudentListServlet via JDBC
    @SuppressWarnings("unchecked")
    List<Student> studentList = (List<Student>) request.getAttribute("studentList");

    // If accessed directly without passing through StudentListServlet, redirect to servlet
    if (studentList == null && request.getAttribute("errorMessage") == null) {
        response.sendRedirect(request.getContextPath() + "/students");
        return;
    }
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiles Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Portal Navigation Bar with Active Session Info -->
    <header class="portal-nav">
        <div class="nav-brand">🎓 Academic Student Portal</div>
        <div class="user-session">
            <span class="welcome-text">Logged in as: <strong><%= currentUser %></strong></span>
            <a href="logout" class="btn btn-outline btn-sm">Sign Out</a>
        </div>
    </header>

    <main class="container">

        <div class="portal-banner">
            <h2>Enrolled Student Directory</h2>
            <p>Displaying student profiles and streamed documents via Java Servlets &amp; JDBC</p>
        </div>

        <%-- Check if any error was reported --%>
        <% 
            String errorMsg = (String) request.getAttribute("errorMessage");
            if (errorMsg != null) { 
        %>
            <div class="alert alert-danger"><%= errorMsg %></div>
        <% } %>

        <!-- Student Profiles Grid -->
        <div class="cards-grid">
            <%
                // Conditional Logic: Check if student list is empty
                if (studentList == null || studentList.isEmpty()) {
            %>
                <div class="empty-state">
                    <p>No student records found in the database.</p>
                </div>
            <%
                } else {
                    // Looping Logic: Iterate through every student record
                    for (Student s : studentList) {
            %>
                <article class="student-profile-card">
                    
                    <div class="card-header">
                        <!-- 
                          Task 3 Requirement: <img> tag pointing directly to FileStreamServlet!
                          The servlet retrieves image bytes from the storage folder and streams them.
                        -->
                        <div class="avatar-wrap">
                            <img src="streamFile?file=<%= s.getPhoto() %>" 
                                 alt="<%= s.getName() %>" 
                                 class="student-avatar" 
                                 loading="lazy"
                                 onerror="this.src='streamFile?file=default_avatar.png';">
                        </div>

                        <div class="student-meta">
                            <h3 class="student-name"><%= s.getName() %></h3>
                            <div class="meta-tags">
                                <span class="badge badge-roll"><%= s.getRollNo() %></span>
                                <span class="badge badge-course"><%= s.getCourse() %></span>
                            </div>
                            <p class="student-email">✉ <%= s.getEmail() %></p>
                        </div>
                    </div>

                    <div class="card-body">
                        <h4 class="section-subtitle">Uploaded Verification Documents:</h4>
                        
                        <%
                            // Conditional Logic: Check if student has uploaded documents
                            List<Student.DocumentItem> docs = s.getDocuments();
                            if (docs == null || docs.isEmpty()) {
                        %>
                            <div class="no-docs-notice">
                                <span>ℹ No verification documents submitted</span>
                            </div>
                        <%
                            } else {
                        %>
                            <!-- Looping Logic: Iterate through uploaded document metadata -->
                            <ul class="documents-list">
                                <% for (Student.DocumentItem doc : docs) { %>
                                    <li class="doc-item">
                                        <div class="doc-info">
                                            <span class="doc-icon">📄</span>
                                            <div>
                                                <div class="doc-name"><%= doc.getFileName() %></div>
                                                <div class="doc-size"><%= doc.getFormattedSize() %> • <%= doc.getUploadDate() %></div>
                                            </div>
                                        </div>
                                        <!-- Stream document directly via FileStreamServlet -->
                                        <a href="streamFile?file=<%= doc.getFilePath() %>" 
                                           target="_blank" 
                                           class="btn btn-stream btn-sm">
                                            View / Stream
                                        </a>
                                    </li>
                                <% } %>
                            </ul>
                        <%
                            } // End of documents conditional logic
                        %>
                    </div>

                    <div class="card-footer">
                        <small>Enrolled on: <%= s.getCreatedAt() %></small>
                    </div>

                </article>
            <%
                    } // End of student list loop
                }
            %>
        </div>

    </main>

</body>
</html>
