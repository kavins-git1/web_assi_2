package com.kpriet.webtech.servlet;

import com.kpriet.webtech.dao.StudentDAO;
import com.kpriet.webtech.model.StudentDocument;

import jakarta.servlet.ServletException;
import jakarta.servlet.ServletOutputStream;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;

/**
 * KPR Institute of Engineering and Technology - Web Technology Assignment II - Q3
 * FileStreamServlet
 * 
 * Streams stored profile images and documents dynamically to client browser.
 * HTML/JSP pages can embed images using: <img src="streamFile?studentId=1&type=profile_image">
 */
@WebServlet("/streamFile")
public class FileStreamServlet extends HttpServlet {

    private StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String studentIdParam = request.getParameter("studentId");
        String typeParam = request.getParameter("type"); // profile_image or id_proof

        if (studentIdParam == null || typeParam == null) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Missing required parameters: studentId and type.");
            return;
        }

        try {
            int studentId = Integer.parseInt(studentIdParam);
            
            // 1. Fetch document metadata from MySQL via JDBC
            StudentDocument doc = studentDAO.getDocumentByType(studentId, typeParam);

            if (doc == null || doc.getFilePath() == null) {
                response.sendError(HttpServletResponse.SC_NOT_FOUND, "Document metadata not found in database.");
                return;
            }

            // Resolve file location (Checks relative path in current workspace or upload folder)
            File file = new File(doc.getFilePath());
            if (!file.exists()) {
                // Try resolving relative to servlet context / php uploads
                String contextPath = getServletContext().getRealPath("/") + doc.getFilePath();
                file = new File(contextPath);
            }

            if (!file.exists()) {
                // Try relative workspace path
                file = new File("c:/Users/kavin/webtechnologies/assignment-2/q2_php_admin_panel/" + doc.getFilePath());
            }

            if (!file.exists()) {
                response.sendError(HttpServletResponse.SC_NOT_FOUND, "Physical file not found on server disk.");
                return;
            }

            // 2. Set Response Headers based on MIME type and length
            String contentType = doc.getFileType();
            if (contentType == null || contentType.isEmpty()) {
                contentType = getServletContext().getMimeType(file.getName());
                if (contentType == null) contentType = "application/octet-stream";
            }

            response.setContentType(contentType);
            response.setContentLength((int) file.length());

            // Inline display for images/pdf, attachment for download
            if (contentType.startsWith("image/") || contentType.equals("application/pdf")) {
                response.setHeader("Content-Disposition", "inline; filename=\"" + doc.getOriginalName() + "\"");
            } else {
                response.setHeader("Content-Disposition", "attachment; filename=\"" + doc.getOriginalName() + "\"");
            }

            // 3. Stream File Bytes from disk to ServletOutputStream
            try (FileInputStream in = new FileInputStream(file);
                 ServletOutputStream out = response.getOutputStream()) {

                byte[] buffer = new byte[8192];
                int bytesRead;
                while ((bytesRead = in.read(buffer)) != -1) {
                    out.write(buffer, 0, bytesRead);
                }
                out.flush();
            }

        } catch (NumberFormatException e) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Invalid student ID format.");
        }
    }
}
