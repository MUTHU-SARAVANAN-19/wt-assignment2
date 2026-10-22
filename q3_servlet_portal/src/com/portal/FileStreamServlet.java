package com.portal;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/**
 * ============================================================================
 * Question 3: FileStreamServlet (Image and Document Streaming)
 * Retrieves files from upload directory and streams raw bytes directly to HTTP response.
 * Used by <img> tags: <img src="streamFile?file=filename.png">
 * And document preview links: <a href="streamFile?file=filename.pdf">
 * ============================================================================
 */
public class FileStreamServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) 
            throws ServletException, IOException {

        String requestedFile = request.getParameter("file");

        if (requestedFile == null || requestedFile.trim().isEmpty()) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "File parameter is required.");
            return;
        }

        // Security: Prevent path traversal attacks by extracting only the clean base filename
        String cleanFileName = new File(requestedFile).getName();

        // 1. Resolve file path: Check webapp uploads directory first
        String webappUploadPath = getServletContext().getRealPath("/uploads");
        File targetFile = null;

        if (webappUploadPath != null) {
            targetFile = new File(webappUploadPath, cleanFileName);
        }

        // 2. If not found in webapp, check shared project uploads folder (q2_php_admin/uploads)
        if (targetFile == null || !targetFile.exists()) {
            // Check direct relative path from workspace or Tomcat working directory
            File sharedDir = new File(getServletContext().getRealPath("/"), "../../q2_php_admin/uploads");
            if (sharedDir.exists()) {
                File candidate = new File(sharedDir, cleanFileName);
                if (candidate.exists()) {
                    targetFile = candidate;
                }
            }
        }

        // 3. Fallback: Check local current directory uploads
        if (targetFile == null || !targetFile.exists()) {
            File localUploads = new File("uploads", cleanFileName);
            if (localUploads.exists()) {
                targetFile = localUploads;
            }
        }

        // 4. If file is still not found, return 404
        if (targetFile == null || !targetFile.exists() || !targetFile.isFile()) {
            response.sendError(HttpServletResponse.SC_NOT_FOUND, "Requested file not found: " + cleanFileName);
            return;
        }

        // 5. Determine MIME Content-Type dynamically
        String mimeType = getServletContext().getMimeType(targetFile.getName());
        if (mimeType == null) {
            String ext = cleanFileName.substring(cleanFileName.lastIndexOf(".") + 1).toLowerCase();
            switch (ext) {
                case "png":
                    mimeType = "image/png";
                    break;
                case "jpg":
                case "jpeg":
                    mimeType = "image/jpeg";
                    break;
                case "pdf":
                    mimeType = "application/pdf";
                    break;
                default:
                    mimeType = "application/octet-stream";
            }
        }

        response.setContentType(mimeType);
        response.setContentLengthLong(targetFile.length());

        // 6. Stream file bytes directly to response output stream
        try (FileInputStream in = new FileInputStream(targetFile);
             OutputStream out = response.getOutputStream()) {

            byte[] buffer = new byte[4096];
            int bytesRead;
            while ((bytesRead = in.read(buffer)) != -1) {
                out.write(buffer, 0, bytesRead);
            }
            out.flush();
        }
    }
}
