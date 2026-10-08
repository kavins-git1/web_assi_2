package com.kpriet.webtech.servlet;

import com.kpriet.webtech.dao.StudentDAO;
import com.kpriet.webtech.model.Student;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

import java.io.IOException;

/**
 * Controller Servlet retrieving individual student profile details and documents
 */
@WebServlet("/profile")
public class StudentProfileServlet extends HttpServlet {

    private StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String idParam = request.getParameter("id");
        if (idParam != null) {
            try {
                int id = Integer.parseInt(idParam);
                Student student = studentDAO.getStudentById(id);
                if (student != null) {
                    request.setAttribute("student", student);
                    request.getRequestDispatcher("/profile.jsp").forward(request, response);
                    return;
                }
            } catch (NumberFormatException ignored) {}
        }
        response.sendRedirect(request.getContextPath() + "/dashboard?error=Student+not+found");
    }
}
