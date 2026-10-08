<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | KPRIET Servlet Portal (Q3)</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/servlet_style.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2 style="margin-bottom: 0.5rem;">KPRIET Admin Login</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 2rem;">
                Question 3: Session-Secured Servlet &amp; JDBC Portal
            </p>

            <% if (request.getParameter("error") != null) { %>
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.88rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <%= request.getParameter("error") %>
                </div>
            <% } %>

            <% if (request.getParameter("msg") != null) { %>
                <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #6ee7b7; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.88rem;">
                    <i class="fa-solid fa-circle-check"></i> <%= request.getParameter("msg") %>
                </div>
            <% } %>

            <form action="login" method="POST">
                <div style="text-align: left;">
                    <label style="font-size: 0.85rem; color: var(--text-muted);">Username</label>
                    <input type="text" name="username" class="input-field" placeholder="e.g. admin" required value="admin">

                    <label style="font-size: 0.85rem; color: var(--text-muted);">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required value="admin123">
                </div>

                <button type="submit" class="btn-portal btn-accent" style="width: 100%; justify-content: center; padding: 0.85rem;">
                    <i class="fa-solid fa-right-to-bracket"></i> Secure Portal Login
                </button>
            </form>

            <div style="margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
                Default Credentials: <code>admin</code> / <code>admin123</code>
            </div>
        </div>
    </div>

</body>
</html>
