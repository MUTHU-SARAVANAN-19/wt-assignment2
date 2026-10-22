package com.portal;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

/**
 * ============================================================================
 * Question 3: LoginServlet (Session Authentication Handling)
 * Verifies credentials in MySQL 'users' table and establishes HttpSession
 * ============================================================================
 */
public class LoginServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {
        // If user already has an active authenticated session, redirect straight to portal
        HttpSession session = request.getSession(false);
        if (session != null && session.getAttribute("user") != null) {
            response.sendRedirect(request.getContextPath() + "/students");
            return;
        }
        // Otherwise forward to login page
        request.getRequestDispatcher("/login.jsp").forward(request, response);
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {
        String username = request.getParameter("username");
        String password = request.getParameter("password");

        if (username == null || password == null || username.trim().isEmpty() || password.trim().isEmpty()) {
            request.setAttribute("errorMessage", "Username and password cannot be empty.");
            request.getRequestDispatcher("/login.jsp").forward(request, response);
            return;
        }

        // Authenticate user against MySQL database
        String sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {

            stmt.setString(1, username.trim());
            stmt.setString(2, password.trim());

            try (ResultSet rs = stmt.executeQuery()) {
                if (rs.next()) {
                    // Valid credentials: Create secure user session
                    HttpSession session = request.getSession(true);
                    session.setAttribute("user", rs.getString("username"));
                    session.setAttribute("role", rs.getString("role"));
                    // Session expires after 30 minutes of inactivity
                    session.setMaxInactiveInterval(30 * 60);

                    // Redirect to secure student list portal
                    response.sendRedirect(request.getContextPath() + "/students");
                    return;
                } else {
                    request.setAttribute("errorMessage", "Invalid username or password. Please try again.");
                    request.getRequestDispatcher("/login.jsp").forward(request, response);
                }
            }

        } catch (Exception e) {
            request.setAttribute("errorMessage", "Database Connection Error: " + e.getMessage());
            request.getRequestDispatcher("/login.jsp").forward(request, response);
        }
    }
}
