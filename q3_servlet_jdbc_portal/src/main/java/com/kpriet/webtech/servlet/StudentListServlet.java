package com.kpriet.webtech.servlet;

import com.kpriet.webtech.dao.StudentDAO;
import com.kpriet.webtech.model.Student;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

import java.io.IOException;
import java.util.List;

/**
 * Controller Servlet retrieving student records via JDBC and forwarding to JSP View
 */
@WebServlet("/dashboard")
public class StudentListServlet extends HttpServlet {

    private StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        List<Student> students = studentDAO.getAllStudents();
        request.setAttribute("studentList", students);
        request.getRequestDispatcher("/dashboard.jsp").forward(request, response);
    }
}
