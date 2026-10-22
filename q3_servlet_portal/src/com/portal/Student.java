package com.portal;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

/**
 * ============================================================================
 * Question 3: Student & Document Model Beans
 * Encapsulates student profile information and attached document metadata
 * ============================================================================
 */
public class Student implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private String name;
    private String rollNo;
    private String email;
    private String course;
    private String photo;
    private String createdAt;
    private List<DocumentItem> documents = new ArrayList<>();

    public Student() {}

    public Student(int id, String name, String rollNo, String email, String course, String photo, String createdAt) {
        this.id = id;
        this.name = name;
        this.rollNo = rollNo;
        this.email = email;
        this.course = course;
        this.photo = photo;
        this.createdAt = createdAt;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getRollNo() { return rollNo; }
    public void setRollNo(String rollNo) { this.rollNo = rollNo; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getCourse() { return course; }
    public void setCourse(String course) { this.course = course; }

    public String getPhoto() { return photo; }
    public void setPhoto(String photo) { this.photo = photo; }

    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }

    public List<DocumentItem> getDocuments() { return documents; }
    public void setDocuments(List<DocumentItem> documents) { this.documents = documents; }
    public void addDocument(DocumentItem doc) { this.documents.add(doc); }

    /**
     * Inner class representing linked document metadata
     */
    public static class DocumentItem implements Serializable {
        private static final long serialVersionUID = 1L;

        private int id;
        private int studentId;
        private String fileName;
        private String filePath;
        private String fileType;
        private int fileSize;
        private String uploadDate;

        public DocumentItem() {}

        public DocumentItem(int id, int studentId, String fileName, String filePath, String fileType, int fileSize, String uploadDate) {
            this.id = id;
            this.studentId = studentId;
            this.fileName = fileName;
            this.filePath = filePath;
            this.fileType = fileType;
            this.fileSize = fileSize;
            this.uploadDate = uploadDate;
        }

        public int getId() { return id; }
        public int getStudentId() { return studentId; }
        public String getFileName() { return fileName; }
        public String getFilePath() { return filePath; }
        public String getFileType() { return fileType; }
        public int getFileSize() { return fileSize; }
        public String getUploadDate() { return uploadDate; }

        public String getFormattedSize() {
            return String.format("%.1f KB", fileSize / 1024.0);
        }
    }
}
