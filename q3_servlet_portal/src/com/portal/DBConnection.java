package com.portal;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * ============================================================================
 * Question 3: JDBC Database Connection Utility
 * Connects Java Servlets to MySQL database 'student_portal'
 * ============================================================================
 */
public class DBConnection {

    // Database connection URL with modern SSL & timezone settings
    private static final String URL = "jdbc:mysql://localhost:3306/student_portal?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC";
    private static final String USER = "root";
    private static final String PASSWORD = ""; // Default XAMPP MySQL root password is empty

    static {
        try {
            // Load MySQL 8+ JDBC Driver class into memory
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            System.err.println("JDBC Driver not found: " + e.getMessage());
        }
    }

    /**
     * Obtains a new database connection
     * @return Connection object
     * @throws SQLException if connection fails
     */
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }
}
