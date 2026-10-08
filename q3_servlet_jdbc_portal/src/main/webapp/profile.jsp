<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="com.kpriet.webtech.model.Student" %>
<%@ page import="com.kpriet.webtech.model.StudentDocument" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile View | Servlet Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/servlet_style.css">
</head>
<body>

    <nav class="portal-nav">
        <div class="brand-title">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>KPRIET Student Profile Viewer</span>
        </div>
        <div>
            <a href="dashboard" class="btn-portal btn-accent"><i class="fa-solid fa-arrow-left"></i> Back to Directory</a>
        </div>
    </nav>

    <div class="main-content" style="max-width: 900px;">
        <% 
            Student s = (Student) request.getAttribute("student");
            if (s != null) {
        %>
            <div class="portal-card">
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    
                    <!-- Dynamic Servlet Streamed Image -->
                    <div style="text-align: center; width: 200px;">
                        <img src="streamFile?studentId=<%= s.getStudentId() %>&type=profile_image" 
                             alt="<%= s.getFullName() %>" 
                             style="width: 170px; height: 170px; object-fit: cover; border-radius: 20px; border: 3px solid var(--portal-accent); box-shadow: var(--shadow);"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<%= s.getFullName().replace(" ", "+") %>&background=6366f1&color=fff';">
                        <h3 style="margin-top: 1rem; color: #fff; font-size: 1.2rem;"><%= s.getFullName() %></h3>
                        <p style="color: var(--text-muted); font-size: 0.85rem;"><%= s.getRollNumber() %></p>
                    </div>

                    <!-- Profile Metadata List -->
                    <div style="flex: 1; min-width: 280px;">
                        <h3 style="color: var(--portal-accent); font-size: 1.2rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                            <i class="fa-solid fa-circle-info"></i> Student Academic File
                        </h3>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; font-size: 0.9rem; color: var(--text-muted);">
                            <div>
                                <span style="font-size: 0.78rem; display: block; color: var(--portal-accent);">Course Enrolled</span>
                                <strong style="color: #fff;"><%= s.getCourse() %></strong>
                            </div>
                            <div>
                                <span style="font-size: 0.78rem; display: block; color: var(--portal-accent);">Institutional Email</span>
                                <strong style="color: #fff;"><%= s.getEmail() %></strong>
                            </div>
                            <div>
                                <span style="font-size: 0.78rem; display: block; color: var(--portal-accent);">Admission Status</span>
                                <span class="status-pill status-<%= s.getAdmissionStatus() %>"><%= s.getAdmissionStatus() %></span>
                            </div>
                            <div>
                                <span style="font-size: 0.78rem; display: block; color: var(--portal-accent);">Registration Timestamp</span>
                                <strong style="color: #fff;"><%= s.getCreatedAt() != null ? s.getCreatedAt().toString() : "N/A" %></strong>
                            </div>
                        </div>

                        <!-- Streamed Document Links -->
                        <h3 style="color: var(--portal-accent); font-size: 1.1rem; margin-top: 2rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                            <i class="fa-solid fa-file-shield"></i> Associated Documents (Servlet Binary Stream)
                        </h3>

                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(15,23,42,0.8); border: 1px solid var(--border-color); padding: 0.75rem 1rem; border-radius: 8px;">
                                <div>
                                    <strong style="color: #fff; font-size: 0.9rem;"><i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i> Student Verification ID Proof</strong>
                                    <p style="font-size: 0.78rem; color: var(--text-muted);">Streamed via <code>FileStreamServlet?studentId=<%= s.getStudentId() %>&type=id_proof</code></p>
                                </div>
                                <a href="streamFile?studentId=<%= s.getStudentId() %>&type=id_proof" target="_blank" class="btn-portal btn-accent" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-eye"></i> View / Stream Document
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <% } else { %>
            <div class="portal-card" style="text-align: center;">
                <h2>Student Profile Not Found</h2>
                <a href="dashboard" class="btn-portal btn-accent" style="margin-top: 1rem;">Back to Directory</a>
            </div>
        <% } %>
    </div>

</body>
</html>
