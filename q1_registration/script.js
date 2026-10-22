/**
 * ============================================================================
 * Question 1: Client-Side Validation Script (JavaScript)
 * Validates Required Fields, Email Format, File Size/Type, and Password Strength
 * ============================================================================
 */

// Wait for DOM to finish loading before binding events
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("studentRegistrationForm");
  const formAlert = document.getElementById("formAlert");

  // Form input elements
  const fullName = document.getElementById("fullName");
  const rollNo = document.getElementById("rollNo");
  const email = document.getElementById("email");
  const course = document.getElementById("course");
  const fileUpload = document.getElementById("fileUpload");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirmPassword");

  // Password strength UI elements
  const strengthBar = document.getElementById("strengthBar");
  const strengthText = document.getElementById("strengthText");
  const critLength = document.getElementById("crit-length");
  const critUpper = document.getElementById("crit-uppercase");
  const critLower = document.getElementById("crit-lowercase");
  const critNum = document.getElementById("crit-number");
  const critSpec = document.getElementById("crit-special");

  /**
   * Helper function to show field error
   * @param {HTMLElement} inputElem
   * @param {string} errorId
   * @param {string} message
   */
  function setError(inputElem, errorId, message) {
    const parent = inputElem.closest(".form-group");
    const errorDisplay = document.getElementById(errorId);
    if (parent) {
      parent.classList.add("has-error");
      parent.classList.remove("has-success");
    }
    if (errorDisplay) {
      errorDisplay.textContent = message;
    }
  }

  /**
   * Helper function to clear field error and mark valid
   * @param {HTMLElement} inputElem
   * @param {string} errorId
   */
  function setSuccess(inputElem, errorId) {
    const parent = inputElem.closest(".form-group");
    const errorDisplay = document.getElementById(errorId);
    if (parent) {
      parent.classList.remove("has-error");
      parent.classList.add("has-success");
    }
    if (errorDisplay) {
      errorDisplay.textContent = "";
    }
  }

  /**
   * 1. Validate Full Name
   * Name must be non-empty and have at least 3 letters
   */
  function validateFullName() {
    const val = fullName.value.trim();
    if (val === "") {
      setError(fullName, "error-fullName", "Full name is required.");
      return false;
    } else if (val.length < 3) {
      setError(fullName, "error-fullName", "Name must contain at least 3 characters.");
      return false;
    } else {
      setSuccess(fullName, "error-fullName");
      return true;
    }
  }

  /**
   * 2. Validate Roll Number
   * Alphanumeric, between 4 and 15 characters
   */
  function validateRollNo() {
    const val = rollNo.value.trim();
    const rollPattern = /^[A-Za-z0-9\-_]{4,15}$/;
    if (val === "") {
      setError(rollNo, "error-rollNo", "Roll Number is required.");
      return false;
    } else if (!rollPattern.test(val)) {
      setError(rollNo, "error-rollNo", "Roll Number must be 4-15 alphanumeric characters.");
      return false;
    } else {
      setSuccess(rollNo, "error-rollNo");
      return true;
    }
  }

  /**
   * 3. Validate Email Address
   * Standard RFC-compliant email regex
   */
  function validateEmail() {
    const val = email.value.trim();
    // Standard robust email pattern
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (val === "") {
      setError(email, "error-email", "Email address is required.");
      return false;
    } else if (!emailPattern.test(val)) {
      setError(email, "error-email", "Please enter a valid email address (e.g., student@domain.com).");
      return false;
    } else {
      setSuccess(email, "error-email");
      return true;
    }
  }

  /**
   * 4. Validate Course Selection
   */
  function validateCourse() {
    const val = course.value;
    if (val === "") {
      setError(course, "error-course", "Please select an admitted course.");
      return false;
    } else {
      setSuccess(course, "error-course");
      return true;
    }
  }

  /**
   * 5. Validate File Upload (Size and Type)
   * Allowed: JPG, JPEG, PNG, PDF | Max size: 2MB
   */
  function validateFileUpload() {
    const files = fileUpload.files;
    if (!files || files.length === 0) {
      setError(fileUpload, "error-fileUpload", "Please upload a photo or ID document.");
      return false;
    }
    const file = files[0];
    const allowedExtensions = ["jpg", "jpeg", "png", "pdf"];
    const fileExt = file.name.split(".").pop().toLowerCase();
    const maxSizeBytes = 2 * 1024 * 1024; // 2MB

    if (!allowedExtensions.includes(fileExt)) {
      setError(fileUpload, "error-fileUpload", "Invalid file type. Only JPG, PNG, and PDF files are allowed.");
      return false;
    }

    if (file.size > maxSizeBytes) {
      setError(fileUpload, "error-fileUpload", "File exceeds maximum size limit of 2MB.");
      return false;
    }

    setSuccess(fileUpload, "error-fileUpload");
    return true;
  }

  /**
   * 6. Real-time Password Strength Evaluation & Criteria Check
   * Calculates score based on length, lowercase, uppercase, numbers, symbols
   */
  function checkPasswordStrength() {
    const val = password.value;
    let score = 0;

    // Individual criteria verification
    const hasLength = val.length >= 8;
    const hasUpper = /[A-Z]/.test(val);
    const hasLower = /[a-z]/.test(val);
    const hasNum = /[0-9]/.test(val);
    const hasSpec = /[^A-Za-z0-9]/.test(val);

    // Toggle criteria list class
    critLength.classList.toggle("valid", hasLength);
    critUpper.classList.toggle("valid", hasUpper);
    critLower.classList.toggle("valid", hasLower);
    critNum.classList.toggle("valid", hasNum);
    critSpec.classList.toggle("valid", hasSpec);

    if (val.length === 0) {
      strengthBar.style.width = "0%";
      strengthBar.style.backgroundColor = "transparent";
      strengthText.textContent = "None";
      strengthText.style.color = "var(--text-muted)";
      return 0;
    }

    if (hasLength) score += 20;
    if (hasUpper) score += 20;
    if (hasLower) score += 20;
    if (hasNum) score += 20;
    if (hasSpec) score += 20;

    strengthBar.style.width = score + "%";

    // Visual indicators based on calculated score
    if (score <= 40) {
      strengthBar.style.backgroundColor = "var(--error-color)";
      strengthText.textContent = "Weak";
      strengthText.style.color = "var(--error-color)";
    } else if (score <= 80) {
      strengthBar.style.backgroundColor = "var(--warning-color)";
      strengthText.textContent = "Medium";
      strengthText.style.color = "var(--warning-color)";
    } else {
      strengthBar.style.backgroundColor = "var(--success-color)";
      strengthText.textContent = "Strong";
      strengthText.style.color = "var(--success-color)";
    }

    return score;
  }

  function validatePassword() {
    const val = password.value;
    const score = checkPasswordStrength();
    if (val === "") {
      setError(password, "error-password", "Password cannot be empty.");
      return false;
    } else if (score < 60) {
      setError(password, "error-password", "Password is too weak. Ensure it meets the criteria above.");
      return false;
    } else {
      setSuccess(password, "error-password");
      return true;
    }
  }

  /**
   * 7. Validate Confirm Password Match
   */
  function validateConfirmPassword() {
    const pwd = password.value;
    const cpwd = confirmPassword.value;
    if (cpwd === "") {
      setError(confirmPassword, "error-confirmPassword", "Please confirm your password.");
      return false;
    } else if (pwd !== cpwd) {
      setError(confirmPassword, "error-confirmPassword", "Passwords do not match.");
      return false;
    } else {
      setSuccess(confirmPassword, "error-confirmPassword");
      return true;
    }
  }

  // Bind real-time input events for instant feedback
  fullName.addEventListener("input", validateFullName);
  rollNo.addEventListener("input", validateRollNo);
  email.addEventListener("input", validateEmail);
  course.addEventListener("change", validateCourse);
  fileUpload.addEventListener("change", validateFileUpload);
  password.addEventListener("input", () => {
    checkPasswordStrength();
    validatePassword();
    if (confirmPassword.value.length > 0) {
      validateConfirmPassword();
    }
  });
  confirmPassword.addEventListener("input", validateConfirmPassword);

  // Form Reset handler to clear visual indicators
  form.addEventListener("reset", () => {
    setTimeout(() => {
      // Clear error classes
      document.querySelectorAll(".form-group").forEach(grp => {
        grp.classList.remove("has-error", "has-success");
      });
      document.querySelectorAll(".error-msg").forEach(el => el.textContent = "");
      // Reset strength indicators
      strengthBar.style.width = "0%";
      strengthText.textContent = "None";
      strengthText.style.color = "var(--text-muted)";
      [critLength, critUpper, critLower, critNum, critSpec].forEach(el => el.classList.remove("valid"));
      // Hide alert
      formAlert.className = "alert-box hidden";
      formAlert.textContent = "";
    }, 10);
  });

  // Form Submit Handler
  form.addEventListener("submit", (e) => {
    e.preventDefault(); // Prevent standard page reload

    // Run all validations
    const isNameValid = validateFullName();
    const isRollValid = validateRollNo();
    const isEmailValid = validateEmail();
    const isCourseValid = validateCourse();
    const isFileValid = validateFileUpload();
    const isPwdValid = validatePassword();
    const isConfirmPwdValid = validateConfirmPassword();

    const isFormValid = isNameValid && isRollValid && isEmailValid &&
                        isCourseValid && isFileValid && isPwdValid && isConfirmPwdValid;

    if (isFormValid) {
      formAlert.className = "alert-box success";
      formAlert.textContent = `✓ Registration successful for ${fullName.value.trim()} (${rollNo.value.trim()})! Details validated.`;
      // Smooth scroll to top of card so alert is visible
      formAlert.scrollIntoView({ behavior: "smooth", block: "center" });
    } else {
      formAlert.className = "alert-box error";
      formAlert.textContent = "⚠ Please fix the highlighted errors above before proceeding.";
      // Find first invalid input and focus it
      const firstInvalid = form.querySelector(".form-group.has-error input, .form-group.has-error select");
      if (firstInvalid) {
        firstInvalid.focus();
      }
    }
  });
});
