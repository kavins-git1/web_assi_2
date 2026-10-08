package com.kpriet.webtech.model;

import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.List;

/**
 * Student Entity Model Class
 */
public class Student {
    private int studentId;
    private String rollNumber;
    private String fullName;
    private String email;
    private String course;
    private String gender;
    private String dob;
    private String phone;
    private String address;
    private String admissionStatus;
    private Timestamp createdAt;
    private List<StudentDocument> documents = new ArrayList<>();
    private String profileImagePath;

    public Student() {}

    public Student(int studentId, String rollNumber, String fullName, String email, String course, String admissionStatus) {
        this.studentId = studentId;
        this.rollNumber = rollNumber;
        this.fullName = fullName;
        this.email = email;
        this.course = course;
        this.admissionStatus = admissionStatus;
    }

    // Getters and Setters
    public int getStudentId() { return studentId; }
    public void setStudentId(int studentId) { this.studentId = studentId; }

    public String getRollNumber() { return rollNumber; }
    public void setRollNumber(String rollNumber) { this.rollNumber = rollNumber; }

    public String getFullName() { return fullName; }
    public void setFullName(String fullName) { this.fullName = fullName; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getCourse() { return course; }
    public void setCourse(String course) { this.course = course; }

    public String getGender() { return gender; }
    public void setGender(String gender) { this.gender = gender; }

    public String getDob() { return dob; }
    public void setDob(String dob) { this.dob = dob; }

    public String getPhone() { return phone; }
    public void setPhone(String phone) { this.phone = phone; }

    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }

    public String getAdmissionStatus() { return admissionStatus; }
    public void setAdmissionStatus(String admissionStatus) { this.admissionStatus = admissionStatus; }

    public Timestamp getCreatedAt() { return createdAt; }
    public void setCreatedAt(Timestamp createdAt) { this.createdAt = createdAt; }

    public List<StudentDocument> getDocuments() { return documents; }
    public void setDocuments(List<StudentDocument> documents) { this.documents = documents; }

    public String getProfileImagePath() { return profileImagePath; }
    public void setProfileImagePath(String profileImagePath) { this.profileImagePath = profileImagePath; }
}
