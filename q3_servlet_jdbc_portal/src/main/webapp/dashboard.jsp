<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="java.util.List" %>
<%@ page import="com.kpriet.webtech.model.Student" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory | Servlet & JDBC Portal (Q3)</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/servlet_style.css">
</head>
<body>

    <!-- Secured Portal Navigation Bar -->
    <nav class="portal-nav">
        <div class="brand-title">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>KPRIET Student Admission Portal</span>
        </div>
        <div class="user-badge">
            <div class="user-info">
                <i class="fa-solid fa-user-check" style="color: #34d399;"></i>
                Session User: <strong><%= session.getAttribute("user") != null ? session.getAttribute("user") : "Admin" %></strong>
            </div>
            <a href="logout" class="btn-portal btn-logout">
                <i class="fa-solid fa-power-off"></i> Logout
            </a>
        </div>
    </nav>

    <div class="main-content">
        <div class="portal-card">
            <div class="portal-header">
                <div>
                    <h2><i class="fa-solid fa-users" style="color: var(--portal-accent);"></i> Student Admissions Directory</h2>
                    <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.2rem;">
                        Fetched dynamically from MySQL database via JDBC and streamed via FileStreamServlet
                    </p>
                </div>
                <div class="meta-badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; font-size: 0.85rem;">
                    Question 3: Java Servlet &amp; JDBC Streamer
                </div>
            </div>

            <!-- Looping over Student Records from Servlet Controller -->
            <div class="portal-grid">
                <% 
                    List<Student> list = (List<Student>) request.getAttribute("studentList");
                    if (list != null && !list.isEmpty()) {
                        for (Student s : list) {
                %>
                    <div class="student-card">
                        <div class="card-top">
                            <!-- Image Tag Pointing directly to FileStreamServlet endpoint -->
                            <img src="streamFile?studentId=<%= s.getStudentId() %>&type=profile_image" 
                                 alt="<%= s.getFullName() %>" 
                                 class="stream-img"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<%= s.getFullName().replace(" ", "+") %>&background=6366f1&color=fff';">
                            <div class="student-name">
                                <h3><%= s.getFullName() %></h3>
                                <p><i class="fa-solid fa-id-badge"></i> <%= s.getRollNumber() %></p>
                            </div>
                        </div>

                        <div class="details-list">
                            <div><i class="fa-solid fa-book"></i> <%= s.getCourse() %></div>
                            <div><i class="fa-solid fa-envelope"></i> <%= s.getEmail() %></div>
                            <div>
                                Status: 
                                <span class="status-pill status-<%= s.getAdmissionStatus() %>">
                                    <%= s.getAdmissionStatus() %>
                                </span>
                            </div>
                        </div>

                        <div style="margin-top: 0.5rem; display: flex; justify-content: flex-end;">
                            <a href="profile?id=<%= s.getStudentId() %>" class="btn-portal btn-accent" style="font-size: 0.82rem; padding: 0.4rem 0.85rem;">
                                <i class="fa-solid fa-file-invoice"></i> View Full Profile & Documents
                            </a>
                        </div>
                    </div>
                <% 
                        }
                    } else {
                %>
                    <div style="grid-column: span 3; text-align: center; color: var(--text-muted); padding: 3rem;">
                        <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; color: var(--portal-accent);"></i>
                        <p>No student records found in MySQL database.</p>
                    </div>
                <% } %>
            </div>
        </div>
    </div>

</body>
</html>
