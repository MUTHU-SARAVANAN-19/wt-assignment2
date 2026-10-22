<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">🔐</div>
            <h2>Student Portal Login</h2>
            <p>Access student profiles, photos, and verification documents</p>
        </div>

        <%-- Display Error Message if authentication failed or unauthorized --%>
        <% 
            String errorMsg = (String) request.getAttribute("errorMessage");
            String errParam = request.getParameter("error");
            String logoutParam = request.getParameter("logout");
            if (errorMsg != null) {
        %>
            <div class="alert alert-danger"><%= errorMsg %></div>
        <% } else if ("unauthorized".equals(errParam)) { %>
            <div class="alert alert-danger">Please login to access the student portal.</div>
        <% } else if ("true".equals(logoutParam)) { %>
            <div class="alert alert-success">You have been logged out successfully.</div>
        <% } %>

        <form action="login" method="post" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="e.g. admin" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In to Portal</button>
        </form>

        <div class="login-footer">
            <small>Default test credentials: <strong>admin</strong> / <strong>password123</strong></small>
        </div>
    </div>

</body>
</html>
