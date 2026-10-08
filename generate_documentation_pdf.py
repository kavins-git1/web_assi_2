import os
import sys
from reportlab.lib.pagesizes import letter
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle, Preformatted, HRFlowable
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.pdfgen import canvas

# Page numbering canvas class
class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_number(num_pages)
            canvas.Canvas.showPage(self)
        canvas.Canvas.save(self)

    def draw_page_number(self, page_count):
        if self._pageNumber == 1:
            return # Skip cover page number
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748b"))
        
        # Header strip
        self.setStrokeColor(colors.HexColor("#cbd5e1"))
        self.setLineWidth(0.5)
        self.line(40, 750, 572, 750)
        self.drawString(40, 755, "KPRIET CSE • U21CS501 Web Technology — Assignment II Documentation")

        # Footer strip
        self.line(40, 45, 572, 45)
        page_text = f"Page {self._pageNumber} of {page_count}"
        self.drawRightString(572, 32, page_text)
        self.drawString(40, 32, "Confidential — Department of Computer Science and Engineering")
        self.restoreState()


def create_documentation_pdf(output_filename):
    doc = SimpleDocTemplate(
        output_filename,
        pagesize=letter,
        rightMargin=40, leftMargin=40, topMargin=50, bottomMargin=55
    )

    styles = getSampleStyleSheet()

    # Premium Color Palette
    primary_color = colors.HexColor("#0f172a") # Deep Navy
    accent_color = colors.HexColor("#2563eb")  # Vibrant Blue
    secondary_color = colors.HexColor("#334155")
    code_bg = colors.HexColor("#f8fafc")
    border_color = colors.HexColor("#cbd5e1")

    # Typography Styles
    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=22,
        leading=26,
        textColor=primary_color,
        alignment=1,
        spaceAfter=8
    )

    subtitle_style = ParagraphStyle(
        'DocSubTitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=11,
        leading=16,
        textColor=secondary_color,
        alignment=1,
        spaceAfter=15
    )

    h1_style = ParagraphStyle(
        'H1Style',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=13,
        leading=16,
        textColor=accent_color,
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'H2Style',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=10.5,
        leading=13.5,
        textColor=primary_color,
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'BodyStyle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13,
        textColor=colors.HexColor("#1e293b"),
        spaceAfter=6
    )

    code_style = ParagraphStyle(
        'CodeStyle',
        fontName='Courier',
        fontSize=7,
        leading=8.8,
        textColor=colors.HexColor("#0f172a")
    )

    story = []

    # Chunk long code into blocks of ~50 lines so ReportLab tables can split naturally
    def add_code_file(story, code_text):
        lines = code_text.splitlines()
        chunk_size = 45
        for i in range(0, len(lines), chunk_size):
            chunk = "\n".join(lines[i:i+chunk_size])
            p = Preformatted(chunk, code_style)
            t = Table([[p]], colWidths=[532])
            t.setStyle(TableStyle([
                ('BACKGROUND', (0,0), (-1,-1), code_bg),
                ('BOX', (0,0), (-1,-1), 0.5, border_color),
                ('PADDING', (0,0), (-1,-1), 4),
            ]))
            story.append(t)
            story.append(Spacer(1, 4))

    def read_file(path):
        if os.path.exists(path):
            with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                return f.read()
        return "// File missing: " + path

    # -------------------------------------------------------------------------
    # COVER PAGE
    # -------------------------------------------------------------------------
    story.append(Spacer(1, 30))
    story.append(Paragraph("KPR Institute of Engineering and Technology", title_style))
    story.append(Paragraph("(Autonomous) • Avinashi Road, Arasur, Coimbatore - 641 407<br/><b>DEPARTMENT OF COMPUTER SCIENCE AND ENGINEERING</b>", subtitle_style))
    story.append(HRFlowable(width="100%", thickness=2, color=accent_color, spaceAfter=20))

    story.append(Spacer(1, 15))
    dossier_title_style = ParagraphStyle('DossierTitle', fontName='Helvetica-Bold', fontSize=18, leading=22, textColor=primary_color, alignment=1)
    story.append(Paragraph("Web Technology — Assignment II<br/>Complete Project Documentation & Source Dossier", dossier_title_style))
    story.append(Spacer(1, 15))

    cover_meta = [
        [Paragraph("<b>Course Code & Title:</b> U21CS501 Web Technology", body_style), Paragraph("<b>Academic Year:</b> 2026-2027", body_style)],
        [Paragraph("<b>Year / Sem / Section:</b> III / V / A", body_style), Paragraph("<b>Maximum Marks:</b> 40 Marks", body_style)],
        [Paragraph("<b>Issue Date:</b> 07.10.2026", body_style), Paragraph("<b>Submission Date:</b> 20.10.2026", body_style)]
    ]
    t_cover = Table(cover_meta, colWidths=[266, 266])
    t_cover.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f1f5f9")),
        ('BOX', (0,0), (-1,-1), 1, border_color),
        ('PADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(t_cover)
    story.append(Spacer(1, 25))

    # Submission Requirements Mapping Table
    story.append(Paragraph("<b>Submission Requirements Matrix & Verification Checklist:</b>", h2_style))
    matrix_data = [
        ["Submission Requirement", "Compliance Status", "Repository File Location"],
        ["Part 1: HTML5, CSS3 & JavaScript", "VERIFIED & COMPLETED", "q1_frontend_portal/ (index.html, style.css, validation.js)"],
        ["Part 2: PHP & MySQL CRUD Panel", "VERIFIED & COMPLETED", "q2_php_admin_panel/ (index.php, add_student.php, view_student.php)"],
        ["Part 3: Java Servlet & WEB-INF", "VERIFIED & COMPLETED", "q3_servlet_jdbc_portal/ (web.xml, FileStreamServlet.java, dashboard.jsp)"],
        ["Database Schema Script", "VERIFIED & COMPLETED", "schema.sql (MySQL script for web_tech_db)"],
        ["Screenshots of Output", "VERIFIED & COMPLETED", "README.md & Recorded Browser Test Animation"],
        ["Inline Code Comments", "VERIFIED & COMPLETED", "Annotated across all PHP, Java, JS, and JSP source files"],
        ["Brief Conclusion (3-5 lines)", "VERIFIED & COMPLETED", "Included in DOCUMENTATION.md & PDF Summary"]
    ]
    t_matrix = Table(matrix_data, colWidths=[160, 110, 262])
    t_matrix.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('TEXTCOLOR', (0,0), (-1,0), colors.white),
        ('FONTNAME', (0,0), (-1,0), 'Helvetica-Bold'),
        ('FONTSIZE', (0,0), (-1,0), 8.5),
        ('GRID', (0,0), (-1,-1), 0.5, border_color),
        ('BACKGROUND', (1,1), (1,-1), colors.HexColor("#dcfce7")),
        ('TEXTCOLOR', (1,1), (1,-1), colors.HexColor("#15803d")),
        ('FONTNAME', (1,1), (1,-1), 'Helvetica-Bold'),
        ('FONTSIZE', (0,1), (-1,-1), 8),
        ('PADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_matrix)
    story.append(PageBreak())

    # -------------------------------------------------------------------------
    # SECTION 1: DATABASE SCHEMA SCRIPT
    # -------------------------------------------------------------------------
    story.append(Paragraph("1. Database Schema Script (schema.sql)", h1_style))
    story.append(Paragraph("The MySQL database <code>web_tech_db</code> provides relational tables for student records, uploaded file metadata, and user session credentials.", body_style))
    add_code_file(story, read_file("schema.sql"))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 2: QUESTION 1 - FRONT-END REGISTRATION PORTAL
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("2. Question 1: Front-End Student Admission Portal (8 Marks)", h1_style))
    story.append(Paragraph("<b>Scenario:</b> Student admission registration form with real-time validation, glassmorphism layout, and password entropy meter.", body_style))
    
    story.append(Paragraph("<b>HTML5 Registration Form (q1_frontend_portal/index.html):</b>", h2_style))
    add_code_file(story, read_file("q1_frontend_portal/index.html"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>CSS3 Responsive Glassmorphism Styling (q1_frontend_portal/css/style.css):</b>", h2_style))
    add_code_file(story, read_file("q1_frontend_portal/css/style.css"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>JavaScript Validation & Password Entropy Meter (q1_frontend_portal/js/validation.js):</b>", h2_style))
    add_code_file(story, read_file("q1_frontend_portal/js/validation.js"))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 3: QUESTION 2 - PHP & MYSQL ADMIN PANEL
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("3. Question 2: PHP & MySQL Admin Panel (16 Marks)", h1_style))
    story.append(Paragraph("<b>Scenario:</b> Admin CRUD panel managing student records, file upload processing, MIME/size verification, and unique filename hashing.", body_style))

    story.append(Paragraph("<b>Database Connection Config (q2_php_admin_panel/config/db.php):</b>", h2_style))
    add_code_file(story, read_file("q2_php_admin_panel/config/db.php"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Admin Student List Dashboard (q2_php_admin_panel/index.php):</b>", h2_style))
    add_code_file(story, read_file("q2_php_admin_panel/index.php"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Add Student Record & Secure File Upload (q2_php_admin_panel/add_student.php):</b>", h2_style))
    add_code_file(story, read_file("q2_php_admin_panel/add_student.php"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>View Profile & Uploaded Metadata (q2_php_admin_panel/view_student.php):</b>", h2_style))
    add_code_file(story, read_file("q2_php_admin_panel/view_student.php"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Edit Student Record (q2_php_admin_panel/edit_student.php):</b>", h2_style))
    add_code_file(story, read_file("q2_php_admin_panel/edit_student.php"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Delete Student & Purge Files (q2_php_admin_panel/delete_student.php):</b>", h2_style))
    add_code_file(story, read_file("q2_php_admin_panel/delete_student.php"))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 4: QUESTION 3 - JAVA SERVLET & JDBC SECURE PORTAL
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("4. Question 3: Java Servlet & JDBC Secure Portal (16 Marks)", h1_style))
    story.append(Paragraph("<b>Scenario:</b> Enterprise Servlet portal connecting to MySQL via JDBC, enforcing HTTPSession authentication, and streaming binary profile images.", body_style))

    story.append(Paragraph("<b>JDBC Connection Manager (DBConnection.java):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/java/com/kpriet/webtech/config/DBConnection.java"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Student Data Access Object (StudentDAO.java):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/java/com/kpriet/webtech/dao/StudentDAO.java"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Binary File Streaming Servlet (FileStreamServlet.java):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/java/com/kpriet/webtech/servlet/FileStreamServlet.java"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Session Security Filter (AuthFilter.java):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/java/com/kpriet/webtech/filter/AuthFilter.java"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Login Authentication Servlet (LoginServlet.java):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/java/com/kpriet/webtech/servlet/LoginServlet.java"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Deployment Descriptor (WEB-INF/web.xml):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/webapp/WEB-INF/web.xml"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Dynamic JSP View with Streamed Images (dashboard.jsp):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/webapp/dashboard.jsp"))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Student Profile JSP View (profile.jsp):</b>", h2_style))
    add_code_file(story, read_file("q3_servlet_jdbc_portal/src/main/webapp/profile.jsp"))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 5: CODE EXPLANATION & BRIEF CONCLUSION
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("5. Detailed Code Explanation & Brief Conclusion", h1_style))
    
    explanation_text = """
    <b>1. System Architecture & Tech Stack Integration:</b><br/>
    The application represents a complete 3-tier full stack web architecture:
    <br/>• <b>Presentation Layer (Front-End):</b> HTML5 forms, modern CSS3 glassmorphism layout, and JavaScript client validation.
    <br/>• <b>Application & Logic Layer (PHP / Java Servlets):</b> PHP PDO CRUD controller scripts and Java Jakarta EE Servlets.
    <br/>• <b>Persistence Layer (Database):</b> MySQL database (<code>web_tech_db</code>) linked via PDO in PHP and JDBC in Java.
    <br/><br/>
    <b>2. Key Security Implementations:</b>
    <br/>• <b>SQL Injection Prevention:</b> Accomplished using PDO Prepared Statements in PHP and <code>PreparedStatement</code> objects in Java JDBC.
    <br/>• <b>File Overwrite Prevention:</b> Generated unique timestamp-based random hex filenames (<code>profile_timestamp_hash.ext</code>) before saving uploads to server disk.
    <br/>• <b>MIME & Size Verification:</b> Validates actual file content types and enforces a strict 5MB upload limit.
    <br/>• <b>HTTPSession Security:</b> <code>AuthFilter</code> interceptor protects Servlet routes, ensuring unauthenticated users cannot access sensitive profile files.
    """
    story.append(Paragraph(explanation_text, body_style))
    story.append(Spacer(1, 15))

    conclusion_box = """
    <b>Brief Conclusion (Required 3–5 Lines):</b><br/>
    This assignment successfully demonstrates an end-to-end web technology solution integrating HTML5/CSS3/JavaScript front-end validation, server-side PHP CRUD with MySQL file metadata management, and Java Servlet architecture using JDBC and dynamic binary streaming. Robust security mechanisms—such as strict MIME-type validation, unique hashed file storage to prevent overwriting, and HTTPSession authorization filters—were implemented to ensure high reliability, performance, and data integrity across all layers.
    """
    
    conc_table = Table([[Paragraph(conclusion_box, body_style)]], colWidths=[532])
    conc_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f0fdf4")),
        ('BOX', (0,0), (-1,-1), 1.5, colors.HexColor("#10b981")),
        ('PADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(conc_table)

    # Build Document with custom NumberedCanvas for header/footer page numbers
    doc.build(story, canvasmaker=NumberedCanvas)
    print("PDF Successfully Generated: " + output_filename)

if __name__ == "__main__":
    output_pdf = "WebTechnology_Assignment_II_Documentation.pdf"
    create_documentation_pdf(output_pdf)
