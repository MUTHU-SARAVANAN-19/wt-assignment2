package com.portal;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

/**
 * ============================================================================
 * Question 3: StudentListServlet (Protected Data Retrieval via JDBC)
 * Enforces session authentication, retrieves student profiles & document
 * metadata from MySQL, and forwards to dynamic JSP view
 * ============================================================================
 */
public class StudentListServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {

        // 1. Session Verification: Ensure only logged-in users can view profiles
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendRedirect(request.getContextPath() + "/login?error=unauthorized");
            return;
        }

        List<Student> students = new ArrayList<>();

        // 2. Fetch Students and Linked Documents from MySQL via JDBC
        String studentSql = "SELECT * FROM students ORDER BY id ASC";
        String docSql = "SELECT * FROM student_documents WHERE student_id = ? ORDER BY upload_date DESC";

        try (Connection conn = DBConnection.getConnection();
             PreparedStatement studentStmt = conn.prepareStatement(studentSql);
             ResultSet rs = studentStmt.executeQuery()) {

            while (rs.next()) {
                Student s = new Student(
                    rs.getInt("id"),
                    rs.getString("name"),
                    rs.getString("roll_no"),
                    rs.getString("email"),
                    rs.getString("course"),
                    rs.getString("photo"),
                    rs.getString("created_at")
                );

                // Fetch documents for current student
                try (PreparedStatement docStmt = conn.prepareStatement(docSql)) {
                    docStmt.setInt(1, s.getId());
                    try (ResultSet docRs = docStmt.executeQuery()) {
                        while (docRs.next()) {
                            Student.DocumentItem doc = new Student.DocumentItem(
                                docRs.getInt("id"),
                                docRs.getInt("student_id"),
                                docRs.getString("file_name"),
                                docRs.getString("file_path"),
                                docRs.getString("file_type"),
                                docRs.getInt("file_size"),
                                docRs.getString("upload_date")
                            );
                            s.addDocument(doc);
                        }
                    }
                }

                students.add(s);
            }

            // 3. Attach student list to request and forward to portal.jsp
            request.setAttribute("studentList", students);
            request.getRequestDispatcher("/portal.jsp").forward(request, response);

        } catch (Exception e) {
            request.setAttribute("errorMessage", "Error fetching student records: " + e.getMessage());
            request.getRequestDispatcher("/portal.jsp").forward(request, response);
        }
    }
}
