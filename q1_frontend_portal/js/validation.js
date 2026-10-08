/**
 * KPR Institute of Engineering and Technology
 * Department of Computer Science and Engineering
 * Course: U21CS501 - Web Technology | Assignment II
 * 
 * Client-Side JavaScript Validation Script for Question 1
 * Handles real-time input validation, password entropy meter,
 * drag-and-drop file upload, file type/size verification, and form submission.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 1. Element References
    // -------------------------------------------------------------------------
    const form = document.getElementById('registrationForm');
    const nameInput = document.getElementById('fullName');
    const rollInput = document.getElementById('rollNumber');
    const emailInput = document.getElementById('email');
    const courseSelect = document.getElementById('course');
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const toggleEye = document.getElementById('toggleEye');

    // Password Strength Meter Elements
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const ruleLength = document.getElementById('ruleLength');
    const ruleUpper = document.getElementById('ruleUpper');
    const ruleNumber = document.getElementById('ruleNumber');
    const ruleSymbol = document.getElementById('ruleSymbol');

    // File Upload Elements (Profile Image & Document)
    const profileInput = document.getElementById('profileImage');
    const profileDropzone = document.getElementById('profileDropzone');
    const profilePreviewPill = document.getElementById('profilePreviewPill');
    const profilePreviewImg = document.getElementById('profilePreviewImg');
    const profileFileName = document.getElementById('profileFileName');
    const removeProfileBtn = document.getElementById('removeProfileBtn');

    const docInput = document.getElementById('idProof');
    const docDropzone = document.getElementById('docDropzone');
    const docPreviewPill = document.getElementById('docPreviewPill');
    const docFileName = document.getElementById('docFileName');
    const removeDocBtn = document.getElementById('removeDocBtn');

    const successCard = document.getElementById('successCard');
    const submittedSummary = document.getElementById('submittedSummary');

    // Maximum allowed file size in bytes (5 MB)
    const MAX_FILE_SIZE = 5 * 1024 * 1024;

    // -------------------------------------------------------------------------
    // 2. Real-Time Input Validation Logic
    // -------------------------------------------------------------------------

    /**
     * Validates Student Full Name
     */
    function validateName() {
        const value = nameInput.value.trim();
        const group = nameInput.closest('.input-group');
        if (value.length >= 3 && /^[a-zA-Z\s.]+$/.test(value)) {
            setValid(group);
            return true;
        } else {
            setInvalid(group, 'Full name must contain at least 3 alphabetic characters.');
            return false;
        }
    }

    /**
     * Validates Roll Number format (e.g. 7377211CS101 or 10-12 alphanumeric)
     */
    function validateRollNumber() {
        const value = rollInput.value.trim().toUpperCase();
        rollInput.value = value; // Auto uppercase for standard format
        const group = rollInput.closest('.input-group');
        const rollRegex = /^[0-9]{4,7}[A-Z]{2,4}[0-9]{3,4}$/; // KPRIET roll pattern format
        if (rollRegex.test(value) || value.length === 12) {
            setValid(group);
            return true;
        } else {
            setInvalid(group, 'Enter valid 12-character Roll Number (e.g., 7377211CS101).');
            return false;
        }
    }

    /**
     * Validates Institutional Email Format using standard Regex
     */
    function validateEmail() {
        const value = emailInput.value.trim();
        const group = emailInput.closest('.input-group');
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (emailRegex.test(value)) {
            setValid(group);
            return true;
        } else {
            setInvalid(group, 'Please enter a valid email address (e.g., student@kpriet.ac.in).');
            return false;
        }
    }

    /**
     * Validates Course Dropdown Selection
     */
    function validateCourse() {
        const value = courseSelect.value;
        const group = courseSelect.closest('.input-group');
        if (value !== '') {
            setValid(group);
            return true;
        } else {
            setInvalid(group, 'Please select a valid academic course from the dropdown.');
            return false;
        }
    }

    /**
     * Evaluates Password Strength & updates visual meter and rules
     */
    function evaluatePasswordStrength() {
        const value = passwordInput.value;
        const group = passwordInput.closest('.input-group');

        let score = 0;
        const hasLength = value.length >= 8;
        const hasUpperLower = /[a-z]/.test(value) && /[A-Z]/.test(value);
        const hasNumber = /[0-9]/.test(value);
        const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value);

        // Update Rule Checklist Indicators
        updateRule(ruleLength, hasLength);
        updateRule(ruleUpper, hasUpperLower);
        updateRule(ruleNumber, hasNumber);
        updateRule(ruleSymbol, hasSymbol);

        if (hasLength) score += 25;
        if (hasUpperLower) score += 25;
        if (hasNumber) score += 25;
        if (hasSymbol) score += 25;

        // Update Progress Bar Color and Width
        strengthBar.style.width = `${score}%`;
        if (score <= 25) {
            strengthBar.style.backgroundColor = '#ef4444';
            strengthText.textContent = 'Weak Password';
            strengthText.style.color = '#ef4444';
        } else if (score <= 75) {
            strengthBar.style.backgroundColor = '#f59e0b';
            strengthText.textContent = 'Medium Strength';
            strengthText.style.color = '#f59e0b';
        } else {
            strengthBar.style.backgroundColor = '#10b981';
            strengthText.textContent = 'Strong Password';
            strengthText.style.color = '#10b981';
        }

        if (score === 100) {
            setValid(group);
            return true;
        } else {
            setInvalid(group, 'Password must satisfy all 4 security criteria listed below.');
            return false;
        }
    }

    /**
     * Validates Profile Image Upload (File type & size)
     */
    function validateProfileImage() {
        const group = profileInput.closest('.input-group');
        const file = profileInput.files[0];
        const errorEl = document.getElementById('profileImageError');

        if (!file) {
            setInvalid(group, 'Profile image is required.');
            return false;
        }

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            setInvalid(group, 'Only JPG or PNG format images are allowed.');
            return false;
        }

        if (file.size > MAX_FILE_SIZE) {
            setInvalid(group, `File size exceeds 5MB limit (${(file.size / (1024 * 1024)).toFixed(2)} MB).`);
            return false;
        }

        setValid(group);
        return true;
    }

    /**
     * Validates Document Upload (PDF / DOCX & size)
     */
    function validateDocument() {
        const group = docInput.closest('.input-group');
        const file = docInput.files[0];

        if (!file) {
            setInvalid(group, 'ID Proof document is required.');
            return false;
        }

        const validTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'];
        if (!validTypes.includes(file.type) && !file.name.endsWith('.pdf') && !file.name.endsWith('.docx')) {
            setInvalid(group, 'Only PDF or DOCX format documents are allowed.');
            return false;
        }

        if (file.size > MAX_FILE_SIZE) {
            setInvalid(group, `File size exceeds 5MB limit (${(file.size / (1024 * 1024)).toFixed(2)} MB).`);
            return false;
        }

        setValid(group);
        return true;
    }

    // Helper functions for class toggling
    function setValid(group) {
        group.classList.remove('is-invalid');
        group.classList.add('is-valid');
    }

    function setInvalid(group, message) {
        group.classList.remove('is-valid');
        group.classList.add('is-invalid');
        const errorMsg = group.querySelector('.error-msg');
        if (errorMsg && message) errorMsg.textContent = message;
    }

    function updateRule(element, isMet) {
        if (isMet) {
            element.classList.add('met');
            element.querySelector('i').className = 'fa-solid fa-circle-check';
        } else {
            element.classList.remove('met');
            element.querySelector('i').className = 'fa-solid fa-circle-dot';
        }
    }

    // -------------------------------------------------------------------------
    // 3. Event Listeners for Live Validation
    // -------------------------------------------------------------------------
    nameInput.addEventListener('input', validateName);
    rollInput.addEventListener('input', validateRollNumber);
    emailInput.addEventListener('input', validateEmail);
    courseSelect.addEventListener('change', validateCourse);
    passwordInput.addEventListener('input', evaluatePasswordStrength);

    // Password Visibility Toggle
    togglePasswordBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleEye.className = type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
    });

    // -------------------------------------------------------------------------
    // 4. File Dropzone & Image Preview Event Handlers
    // -------------------------------------------------------------------------
    setupDropzone(profileDropzone, profileInput, handleProfileSelect);
    setupDropzone(docDropzone, docInput, handleDocSelect);

    function setupDropzone(dropzone, input, selectCallback) {
        dropzone.addEventListener('click', (e) => {
            if (!e.target.closest('.remove-file-btn')) {
                input.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                input.files = files;
                selectCallback();
            }
        });

        input.addEventListener('change', selectCallback);
    }

    function handleProfileSelect() {
        if (profileInput.files && profileInput.files[0]) {
            const file = profileInput.files[0];
            if (validateProfileImage()) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    profilePreviewImg.src = e.target.result;
                    profileFileName.textContent = file.name;
                    profilePreviewPill.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    }

    function handleDocSelect() {
        if (docInput.files && docInput.files[0]) {
            const file = docInput.files[0];
            if (validateDocument()) {
                docFileName.textContent = file.name;
                docPreviewPill.classList.remove('hidden');
            }
        }
    }

    removeProfileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileInput.value = '';
        profilePreviewPill.classList.add('hidden');
        profileInput.closest('.input-group').classList.remove('is-valid', 'is-invalid');
    });

    removeDocBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        docInput.value = '';
        docPreviewPill.classList.add('hidden');
        docInput.closest('.input-group').classList.remove('is-valid', 'is-invalid');
    });

    // -------------------------------------------------------------------------
    // 5. Form Submission Handling
    // -------------------------------------------------------------------------
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        // Run all validators
        const isNameValid = validateName();
        const isRollValid = validateRollNumber();
        const isEmailValid = validateEmail();
        const isCourseValid = validateCourse();
        const isPassValid = evaluatePasswordStrength();
        const isProfileValid = validateProfileImage();
        const isDocValid = validateDocument();

        const allValid = isNameValid && isRollValid && isEmailValid && isCourseValid && isPassValid && isProfileValid && isDocValid;

        if (allValid) {
            // Render summary receipt in success card
            submittedSummary.innerHTML = `
                <p><strong>Full Name:</strong> ${nameInput.value.trim()}</p>
                <p><strong>Roll Number:</strong> ${rollInput.value.trim()}</p>
                <p><strong>Email Address:</strong> ${emailInput.value.trim()}</p>
                <p><strong>Course:</strong> ${courseSelect.value}</p>
                <p><strong>Profile Photo:</strong> ${profileInput.files[0].name} (${(profileInput.files[0].size / 1024).toFixed(1)} KB)</p>
                <p><strong>ID Document:</strong> ${docInput.files[0].name} (${(docInput.files[0].size / 1024).toFixed(1)} KB)</p>
            `;

            form.classList.add('hidden');
            successCard.classList.remove('hidden');
            window.scrollTo({ top: successCard.offsetTop - 50, behavior: 'smooth' });
        } else {
            // Focus on first invalid field
            const firstInvalid = form.querySelector('.is-invalid input, .is-invalid select');
            if (firstInvalid) firstInvalid.focus();
        }
    });
});
