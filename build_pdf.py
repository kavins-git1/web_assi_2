import os
import sys
from reportlab.lib.pagesizes import letter
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle, Preformatted, HRFlowable
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors

def create_assignment_pdf(output_filename):
    doc = SimpleDocTemplate(
        output_filename,
        pagesize=letter,
        rightMargin=40, leftMargin=40, topMargin=40, bottomMargin=40
    )

    styles = getSampleStyleSheet()

    # Custom Color Palette
    primary_color = colors.HexColor("#0f172a")
    accent_color = colors.HexColor("#2563eb")
    secondary_color = colors.HexColor("#475569")
    code_bg = colors.HexColor("#f8fafc")
    border_color = colors.HexColor("#cbd5e1")

    # Custom Typography Styles
    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=20,
        leading=24,
        textColor=primary_color,
        alignment=1, # Center
        spaceAfter=6
    )

    subtitle_style = ParagraphStyle(
        'DocSubTitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=11,
        leading=15,
        textColor=secondary_color,
        alignment=1,
        spaceAfter=15
    )

    h1_style = ParagraphStyle(
        'H1Style',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=14,
        leading=17,
        textColor=accent_color,
        spaceBefore=12,
        spaceAfter=8
    )

    h2_style = ParagraphStyle(
        'H2Style',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=14,
        textColor=primary_color,
        spaceBefore=10,
        spaceAfter=6
    )

    body_style = ParagraphStyle(
        'BodyStyle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=13.5,
        textColor=colors.HexColor("#1e293b"),
        spaceAfter=6
    )

    code_style = ParagraphStyle(
        'CodeStyle',
        fontName='Courier',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#0f172a")
    )

    story = []

    # Helper function to wrap code in a styled box
    def make_code_block(code_text):
        p = Preformatted(code_text, code_style)
        t = Table([[p]], colWidths=[520])
        t.setStyle(TableStyle([
            ('BACKGROUND', (0,0), (-1,-1), code_bg),
            ('BOX', (0,0), (-1,-1), 0.5, border_color),
            ('PADDING', (0,0), (-1,-1), 6),
        ]))
        return t

    # Helper function to read file content
    def get_file_content(path):
        if os.path.exists(path):
            with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                return f.read()
        return "// File not found: " + path

    # -------------------------------------------------------------------------
    # COVER / HEADER BLOCK
    # -------------------------------------------------------------------------
    story.append(Paragraph("KPR Institute of Engineering and Technology", title_style))
    story.append(Paragraph("(Autonomous) • Avinashi Road, Arasur, Coimbatore - 641 407<br/><b>Department of Computer Science and Engineering</b>", subtitle_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=accent_color, spaceAfter=12))

    meta_table_data = [
        [Paragraph("<b>Course Code & Title:</b> U21CS501 Web Technology", body_style), Paragraph("<b>Academic Year:</b> 2026-2027", body_style)],
        [Paragraph("<b>Year / Sem / Sec:</b> III / V / A", body_style), Paragraph("<b>Assignment:</b> II (Maximum Marks: 40)", body_style)],
        [Paragraph("<b>Issue Date:</b> 07.10.2026", body_style), Paragraph("<b>Submission Date:</b> 20.10.2026", body_style)]
    ]
    meta_table = Table(meta_table_data, colWidths=[260, 260])
    meta_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f1f5f9")),
        ('BOX', (0,0), (-1,-1), 1, border_color),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('PADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(meta_table)
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SUBMISSION REQUIREMENTS CHECKLIST
    # -------------------------------------------------------------------------
    story.append(Paragraph("Submission Requirements Compliance Checklist", h1_style))
    req_data = [
        ["Requirement Item", "Status", "Mapped File Path in Repository"],
        ["Front-End Files (.html, .css, .js)", "COMPLETED", "q1_frontend_portal/ (index.html, style.css, validation.js)"],
        ["PHP & CRUD Files (.php)", "COMPLETED", "q2_php_admin_panel/ (index.php, add_student.php, view_student.php)"],
        ["Servlet & WEB-INF Files", "COMPLETED", "q3_servlet_jdbc_portal/ (web.xml, FileStreamServlet.java, dashboard.jsp)"],
        ["DB Schema Script", "COMPLETED", "schema.sql (MySQL script for web_tech_db)"],
        ["Screenshots / Output", "COMPLETED", "README.md & Recorded Browser Test Animation"],
        ["Inline Code Comments", "COMPLETED", "Annotated across all PHP, Java, JS, and JSP source files"],
        ["Brief Conclusion (3-5 lines)", "COMPLETED", "Included in DOCUMENTATION.md & PDF summary section"]
    ]
    req_table = Table(req_data, colWidths=[150, 70, 300])
    req_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('TEXTCOLOR', (0,0), (-1,0), colors.white),
        ('FONTNAME', (0,0), (-1,0), 'Helvetica-Bold'),
        ('FONTSIZE', (0,0), (-1,0), 9),
        ('BOTTOMPADDING', (0,0), (-1,0), 6),
        ('GRID', (0,0), (-1,-1), 0.5, border_color),
        ('BACKGROUND', (1,1), (1,-1), colors.HexColor("#dcfce7")),
        ('TEXTCOLOR', (1,1), (1,-1), colors.HexColor("#15803d")),
        ('FONTNAME', (1,1), (1,-1), 'Helvetica-Bold'),
        ('FONTSIZE', (0,1), (-1,-1), 8.5),
        ('PADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(req_table)
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 1: DATABASE SCHEMA SCRIPT (schema.sql)
    # -------------------------------------------------------------------------
    story.append(Paragraph("1. Database Schema Script (schema.sql)", h1_style))
    story.append(Paragraph("The relational database <code>web_tech_db</code> stores student records, file upload metadata, and login user credentials.", body_style))
    
    schema_code = get_file_content("schema.sql")
    schema_snippet = "\n".join(schema_code.splitlines()[:50]) + "\n-- ... [Full schema loaded in schema.sql]"

    story.append(make_code_block(schema_snippet))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 2: QUESTION 1 - FRONT-END REGISTRATION PORTAL
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("2. Question 1: Front-End Student Admission Portal (8 Marks)", h1_style))
    story.append(Paragraph("<b>Scenario:</b> Student admission registration form with real-time validation, responsive layout, and password entropy strength bar.", body_style))
    
    story.append(Paragraph("<b>HTML5 Registration Form (q1_frontend_portal/index.html):</b>", h2_style))
    html_code = get_file_content("q1_frontend_portal/index.html")
    html_snippet = "\n".join(html_code.splitlines()[:45]) + "\n<!-- ... [See index.html for complete markup] -->"
    story.append(make_code_block(html_snippet))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>JavaScript Validation & Password Entropy Bar (q1_frontend_portal/js/validation.js):</b>", h2_style))
    js_code = get_file_content("q1_frontend_portal/js/validation.js")
    js_snippet = "\n".join(js_code.splitlines()[:45]) + "\n// ... [See validation.js for complete client logic]"
    story.append(make_code_block(js_snippet))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 3: QUESTION 2 - PHP & MYSQL ADMIN PANEL
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("3. Question 2: PHP & MySQL Admin Panel (16 Marks)", h1_style))
    story.append(Paragraph("<b>Scenario:</b> Admin CRUD panel managing student records, file upload processing, size/MIME verification, and overwrite prevention.", body_style))

    story.append(Paragraph("<b>Database Connection Module (q2_php_admin_panel/config/db.php):</b>", h2_style))
    db_code = get_file_content("q2_php_admin_panel/config/db.php")
    story.append(make_code_block(db_code))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Add Student & File Upload Processing (q2_php_admin_panel/add_student.php):</b>", h2_style))
    php_code = get_file_content("q2_php_admin_panel/add_student.php")
    php_snippet = "\n".join(php_code.splitlines()[80:130])
    story.append(make_code_block(php_snippet))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 4: QUESTION 3 - JAVA SERVLET & JDBC SECURE PORTAL
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("4. Question 3: Java Servlet & JDBC Secure Portal (16 Marks)", h1_style))
    story.append(Paragraph("<b>Scenario:</b> Secure portal retrieving student data via JDBC, enforcing HTTPSession login security, and streaming binary profile files.", body_style))

    story.append(Paragraph("<b>Binary File Streamer Servlet (FileStreamServlet.java):</b>", h2_style))
    servlet_code = get_file_content("q3_servlet_jdbc_portal/src/main/java/com/kpriet/webtech/servlet/FileStreamServlet.java")
    servlet_snippet = "\n".join(servlet_code.splitlines()[25:75])
    story.append(make_code_block(servlet_snippet))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Deployment Descriptor (WEB-INF/web.xml):</b>", h2_style))
    webxml_code = get_file_content("q3_servlet_jdbc_portal/src/main/webapp/WEB-INF/web.xml")
    story.append(make_code_block(webxml_code))
    story.append(Spacer(1, 10))

    story.append(Paragraph("<b>Dynamic JSP View with Streamed Image Tags (dashboard.jsp):</b>", h2_style))
    jsp_code = get_file_content("q3_servlet_jdbc_portal/src/main/webapp/dashboard.jsp")
    jsp_snippet = "\n".join(jsp_code.splitlines()[:45])
    story.append(make_code_block(jsp_snippet))
    story.append(Spacer(1, 14))

    # -------------------------------------------------------------------------
    # SECTION 5: CONCLUSION & CODE EXPLANATION SUMMARY
    # -------------------------------------------------------------------------
    story.append(PageBreak())
    story.append(Paragraph("5. Code Explanation & Brief Conclusion", h1_style))
    
    explanation_text = """
    <b>System Architecture & Implementation Summary:</b><br/>
    The full stack web technology solution connects front-end HTML5/CSS3/JavaScript validation with server-side PHP/MySQL CRUD and enterprise Java Servlet architecture.<br/><br/>
    <b>Key Security & Reliability Features:</b>
    <br/>• <b>SQL Injection Prevention:</b> Implemented via PDO Prepared Statements in PHP and PreparedStatement objects in Java JDBC.
    <br/>• <b>File Overwrite Prevention:</b> Generated unique timestamp-based hex filenames (<code>profile_timestamp_hash.ext</code>) before saving uploads to disk.
    <br/>• <b>MIME & Size Verification:</b> Validates file content types and enforces a strict 5MB upload limit.
    <br/>• <b>HTTPSession Security:</b> AuthFilter interceptor protects Servlet routes, ensuring unauthenticated users cannot access sensitive profile files.
    """
    story.append(Paragraph(explanation_text, body_style))
    story.append(Spacer(1, 14))

    conclusion_box = """
    <b>Brief Conclusion (Required 3–5 Lines):</b><br/>
    This assignment successfully demonstrates an end-to-end web technology solution integrating HTML5/CSS3/JavaScript front-end validation, server-side PHP CRUD with MySQL file metadata management, and Java Servlet architecture using JDBC and dynamic binary streaming. Robust security mechanisms—such as strict MIME-type validation, unique hashed file storage to prevent overwriting, and HTTPSession authorization filters—were implemented to ensure high reliability, performance, and data integrity across all layers.
    """
    
    conc_table = Table([[Paragraph(conclusion_box, body_style)]], colWidths=[520])
    conc_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f0fdf4")),
        ('BOX', (0,0), (-1,-1), 1.5, colors.HexColor("#10b981")),
        ('PADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(conc_table)

    # Build Document
    doc.build(story)
    print("PDF Successfully Generated: " + output_filename)

if __name__ == "__main__":
    output_pdf = "KPRIET_WebTech_Assignment_2_Submission.pdf"
    create_assignment_pdf(output_pdf)
