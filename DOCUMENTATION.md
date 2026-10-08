# Technical Implementation & Code Walkthrough
## Course: U21CS501 Web Technology - Assignment II
**KPR Institute of Engineering and Technology**

---

### Table of Contents
1. [Question 1: Front-End Student Registration Portal](#question-1-front-end-student-registration-portal)
2. [Question 2: PHP & MySQL Admin Panel (CRUD & File Storage)](#question-2-php--mysql-admin-panel-crud--file-storage)
3. [Question 3: Java Servlet, JDBC & Dynamic Streaming Portal](#question-3-java-servlet-jdbc--dynamic-streaming-portal)
4. [Database Architecture & SQL Schema Script](#database-architecture--sql-schema-script)
5. [Conclusion](#conclusion)

---

### Question 1: Front-End Student Registration Portal

#### 1. Overview
The front-end registration portal allows prospective students to enter academic details, upload profile photos and ID proof documents, and receive real-time feedback.

#### 2. Key Code Highlights
* **HTML structure (`q1_frontend_portal/index.html`):** Utilizes semantic HTML5 inputs, custom file drag-and-drop zones, and accessible ARIA attributes.
* **CSS Styling (`q1_frontend_portal/css/style.css`):** Features CSS custom properties (`var(--primary)`, `var(--accent)`), smooth glassmorphism effects (`backdrop-filter`), background gradient blobs with `@keyframes`, and responsive grid layout.
* **JavaScript Validation (`q1_frontend_portal/js/validation.js`):**
  * **Email Regex:**
    ```javascript
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    ```
  * **Password Strength Meter:** Calculates entropy score based on length (>=8), case variation, numbers, and special characters (`@`, `#`, `$`, etc.), dynamically updating progress bar width and color.
  * **File Validation:** Restricts uploads to JPG/PNG images and PDF/DOCX documents under 5MB (5,242,880 bytes).

---

### Question 2: PHP & MySQL Admin Panel (CRUD & File Storage)

#### 1. Overview
The PHP admin panel provides full management of student profiles and document metadata in MySQL.

#### 2. Features Implemented
1. **PDO Database Connection (`config/db.php`):** Establishes secure connection with prepared statements and `ERRMODE_EXCEPTION`.
2. **CRUD Operations:**
   * **Create (`add_student.php`):** Inserts student data and handles dual file upload (profile photo & ID document).
   * **Read (`index.php` & `view_student.php`):** Lists records with search filter and displays student profile cards with embedded uploaded files.
   * **Update (`edit_student.php`):** Modifies student information and replaces old profile photo if a new file is uploaded.
   * **Delete (`delete_student.php`):** Removes student record from MySQL and calls `unlink()` on disk paths to prevent orphaned files.
3. **File Upload Security & Overwrite Prevention:**
   * Unique filename generation: `profile_` + `time()` + `random_bytes(6)` + `.extension`
   * Strict MIME-type checking using `mime_content_type()`.
   * Metadata storage in `student_documents` table (`file_path`, `file_type`, `file_size`, `upload_date`).

---

### Question 3: Java Servlet, JDBC & Dynamic Streaming Portal

#### 1. Overview
The Java web application connects to MySQL via JDBC, manages session authentication, and streams stored binary images/documents dynamically to the browser.

#### 2. Architecture & Components
* **`DBConnection.java`:** Loads `com.mysql.cj.jdbc.Driver` and provides connection pooling endpoint to MySQL `web_tech_db`.
* **`StudentDAO.java`:** Encapsulates SQL queries using `PreparedStatement` to retrieve student models and document metadata.
* **`AuthFilter.java`:** Servlet Filter intercepting requests to protect endpoints (`/dashboard`, `/profile`, `/streamFile`). Redirects unauthenticated users to `login.jsp`.
* **`FileStreamServlet.java` (Binary Streamer):**
  ```java
  // Fetches metadata via JDBC, sets HTTP headers, and streams bytes directly
  response.setContentType(doc.getFileType());
  response.setContentLength((int) file.length());
  try (FileInputStream in = new FileInputStream(file);
       ServletOutputStream out = response.getOutputStream()) {
      byte[] buffer = new byte[8192];
      int bytesRead;
      while ((bytesRead = in.read(buffer)) != -1) {
          out.write(buffer, 0, bytesRead);
      }
      out.flush();
  }
  ```
* **Dynamic JSP Rendering (`dashboard.jsp` & `profile.jsp`):** HTML `<img>` tags point directly to `FileStreamServlet`:
  ```html
  <img src="streamFile?studentId=1&type=profile_image" class="stream-img" />
  ```

---

### Database Architecture & SQL Schema Script

```sql
CREATE DATABASE IF NOT EXISTS `web_tech_db`;
USE `web_tech_db`;

CREATE TABLE `students` (
    `student_id` INT AUTO_INCREMENT PRIMARY KEY,
    `roll_number` VARCHAR(20) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `course` VARCHAR(100) NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
    `dob` DATE DEFAULT NULL,
    `phone` VARCHAR(15) DEFAULT NULL,
    `admission_status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Approved',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `student_documents` (
    `document_id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `document_type` ENUM('profile_image', 'id_proof', 'certificate') NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL UNIQUE,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` INT NOT NULL,
    `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### Conclusion

> The complete Web Technology assignment has been engineered following modern standards, implementing clean separation of concerns across HTML5/CSS3/JS, PHP/MySQL, and Java Servlet/JDBC technology stacks. All validation rules, database metadata storage, file security checks, and session authorization mechanisms are fully operational and verified.
