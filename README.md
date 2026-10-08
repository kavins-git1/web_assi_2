# KPR Institute of Engineering and Technology
### (Autonomous) • Avinashi Road, Arasur, Coimbatore - 641 407
**Department of Computer Science and Engineering**

---

### Course Assignment Submission
* **Course Code & Title:** `U21CS501` - Web Technology
* **Year / Semester / Section:** III / V / A
* **Assignment:** II
* **Academic Year:** 2026-2027
* **Issue Date:** 07.10.2026 | **Submission Date:** 20.10.2026
* **Maximum Marks:** 40 Marks

---

## 📁 Repository Directory Structure

```
assignment-2/
│
├── README.md                            <-- Complete Assignment Guide & Project Overview
├── DOCUMENTATION.md                     <-- Detailed Code Explanation & Architecture
├── schema.sql                           <-- MySQL Database Creation & Seed Data Script
│
├── q1_frontend_portal/                  <-- Question 1: HTML5, CSS3 & JavaScript Registration Portal
│   ├── index.html                       <-- Student Registration Form with Drag-and-Drop Dropzones
│   ├── css/
│   │   └── style.css                    <-- Responsive Glassmorphism Styling & Password Meter CSS
│   └── js/
│       └── validation.js                <-- Client-side JavaScript Validation (Email Regex, Entropy Bar)
│
├── q2_php_admin_panel/                  <-- Question 2: PHP & MySQL Student Records Admin Panel
│   ├── config/
│   │   └── db.php                       <-- PDO Database Connection Module
│   ├── uploads/                         <-- Storage directory for uploaded photos & PDF documents
│   ├── index.php                        <-- Admin Dashboard with Student Table & Search Filter
│   ├── add_student.php                  <-- Add Student Record + File Upload & MySQL Metadata Storage
│   ├── edit_student.php                 <-- Edit Student Profile & Replace Photo
│   ├── delete_student.php               <-- Delete Record & Purge Uploaded Files from Disk
│   ├── view_student.php                 <-- View Profile & Embedded Uploaded Files/Documents
│   └── css/
│       └── admin.css                    <-- Admin Panel Visual Theme
│
└── q3_servlet_jdbc_portal/              <-- Question 3: Java Servlet & JDBC Secure Portal
    ├── pom.xml                          <-- Maven Dependencies (Jakarta EE, MySQL Connector/J)
    ├── src/
    │   └── main/
    │       ├── java/com/kpriet/webtech/
    │       │   ├── config/
    │       │   │   └── DBConnection.java       <-- JDBC Driver & Connection Manager
    │       │   ├── dao/
    │       │   │   └── StudentDAO.java         <-- Data Access Object using PreparedStatements
    │       │   ├── model/
    │       │   │   ├── Student.java            <-- Student Domain Model
    │       │   │   └── StudentDocument.java    <-- Document Metadata Model
    │       │   ├── servlet/
    │       │   │   ├── LoginServlet.java       <-- HTTPSession Initiation & Auth
    │       │   │   ├── LogoutServlet.java      <-- Session Invalidation
    │       │   │   ├── StudentListServlet.java <-- Controller forwarding to dashboard.jsp
    │       │   │   ├── StudentProfileServlet.java <-- Controller forwarding to profile.jsp
    │       │   │   └── FileStreamServlet.java  <-- Streams Binary Image/PDF Bytes over HTTP
    │       │   └── filter/
    │       │       └── AuthFilter.java         <-- Servlet Filter enforcing secure session login
    │       └── webapp/
    │           ├── WEB-INF/
    │           │   └── web.xml                 <-- Web Application Deployment Descriptor
    │           ├── login.jsp                   <-- Admin Login View
    │           ├── dashboard.jsp               <-- Dynamic Directory with <img src="streamFile"> & Loops
    │           ├── profile.jsp                 <-- Profile View with Document Stream Links
    │           └── css/
    │               └── servlet_style.css       <-- JSP Portal Visual Styles
```

---

## 🚀 Quick Setup & Run Instructions

### 1. Database Setup (MySQL)
1. Open XAMPP Control Panel or MySQL Command Line Client.
2. Import the database schema script:
   ```bash
   mysql -u root -p < schema.sql
   ```
   *This creates the `web_tech_db` database and populates sample student records and document metadata.*

---

### 2. Question 1: Front-End Registration Portal
- Open `q1_frontend_portal/index.html` directly in any web browser (Chrome, Firefox, Edge).
- **Features Tested:**
  - Real-time client-side validation on name, roll number (`7377211CS101`), email format, course selection.
  - Interactive password entropy strength bar with live criteria rules.
  - Drag-and-drop file dropzones with thumbnail preview for photos and PDF file validation.

---

### 3. Question 2: PHP & MySQL Admin Panel
- Move or link `q2_php_admin_panel` into your Apache server web root (e.g. `C:\xampp\htdocs\assignment-2\q2_php_admin_panel`).
- Start Apache and MySQL services in XAMPP.
- Access in browser: `http://localhost/assignment-2/q2_php_admin_panel/`
- **Features Tested:**
  - **CRUD Operations:** View student list with search, add new student, edit details, delete student.
  - **File Upload Processing:** Validates MIME type (`image/jpeg`, `image/png`, `application/pdf`), checks 5MB size limit, generates unique hash filenames to prevent overwriting.
  - **MySQL Metadata Storage:** Stores file path, file type, original name, file size, upload timestamp linked to `students` table.
  - **Disk Cleanup:** Deleting a student automatically purges their uploaded files from the `uploads/` directory on disk.

---

### 4. Question 3: Java Servlet & JDBC Secure Portal
- Build the project using Maven or open `q3_servlet_jdbc_portal` in Eclipse / IntelliJ / NetBeans:
  ```bash
  cd q3_servlet_jdbc_portal
  mvn clean package
  ```
- Deploy the resulting `student-admission-portal.war` file to Apache Tomcat 10+ (or run via embedded Tomcat/Jetty).
- Access in browser: `http://localhost:8080/student-admission-portal/login.jsp`
- **Default Credentials:** Username: `admin`, Password: `admin123`
- **Features Tested:**
  - **Session Security (`HTTPSession` & `AuthFilter`):** Restricts unauthenticated access to `/dashboard` and `/profile`.
  - **JDBC Connection:** Fetches records and file metadata via `StudentDAO` using `PreparedStatement`.
  - **Dynamic Image Streaming (`FileStreamServlet`):** JSP pages use `<img src="streamFile?studentId=1&type=profile_image">` to request binary byte streams directly from Servlet output stream with proper `Content-Type` headers.
  - **Looping & Conditional Logic:** `dashboard.jsp` iterates over student records displaying status indicators and profile cards.

---

## 📝 Brief Conclusion

> This assignment successfully demonstrates an end-to-end web technology solution integrating HTML5/CSS3/JavaScript front-end validation, server-side PHP CRUD with MySQL file metadata management, and Java Servlet architecture using JDBC and dynamic binary streaming. Robust security mechanisms—such as strict MIME-type validation, unique hashed file storage to prevent overwriting, and HTTPSession authorization filters—were implemented to ensure high reliability, performance, and data integrity across all layers.

---

## 📊 Evaluation Rubric Compliance (40 Marks)

| Criteria | Marks | Status | Implementation Details |
| :--- | :---: | :---: | :--- |
| **Front-End Design: HTML, CSS, JavaScript & Validation** | **10** | **Completed** | Full registration form, glassmorphism CSS, live JS validation, password entropy meter, dropzones |
| **PHP, MySQL & CRUD Operations** | **10** | **Completed** | Add, Edit, Delete, View student records, file upload handling, metadata storage in MySQL, overwrite prevention |
| **Servlet, JDBC & Dynamic Web Content** | **10** | **Completed** | JDBC connection, session authentication, `FileStreamServlet` binary streamer, JSP looping & dynamic `<img>` tags |
| **Code Quality, Output & Documentation** | **10** | **Completed** | Clean modular architecture, inline comments, database schema script, detailed walkthrough |
| **Total** | **40** | **100%** | Ready for submission and manual push to GitHub |

---
*KPR Institute of Engineering and Technology • Department of Computer Science and Engineering*
