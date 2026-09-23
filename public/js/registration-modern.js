// ========================================
// REGISTRATION SYSTEM - MODERN JAVASCRIPT
// ========================================

class StudentRegistration {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.formData = {};
        this.faceData = null;
        this.idPhotoFile = null;
        this.video = null;
        this.canvas = null;
        this.stream = null;
        this.faceDetectionReady = false;
        this.faceDescriptor = null;
        this.ocrData = null;
        
        this.init();
    }
    
    init() {
        this.cacheElements();
        this.bindEvents();
        this.showStep(1);
        this.initializeFaceDetection();
    }
    
    cacheElements() {
        this.form = document.getElementById('registrationForm');
        this.stepButtons = document.querySelectorAll('[data-step]');
        this.formSections = document.querySelectorAll('.form-section');
        this.progressSteps = document.querySelectorAll('.progress-step');
        this.nextBtn = document.getElementById('nextBtn');
        this.prevBtn = document.getElementById('prevBtn');
        this.submitBtn = document.getElementById('submitBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        this.captureBtn = document.getElementById('captureBtn');
        this.faceStatus = document.getElementById('faceStatus');
        this.idPhotoInput = document.getElementById('idPhoto');
        this.idPhotoPreview = document.getElementById('idPhotoPreview');
        this.idPhotoArea = document.getElementById('idPhotoArea');
        this.studentIdInput = document.getElementById('studentId');
        this.idNameInput = document.getElementById('idName');
        this.fullNameInput = document.getElementById('fullName');
        this.usernameInput = document.getElementById('username');
        this.passwordInput = document.getElementById('password');
        this.confirmPasswordInput = document.getElementById('confirmPassword');
        this.departmentInput = document.getElementById('department');
        this.sectionInput = document.getElementById('section');
        this.courseInput = document.getElementById('course');
        this.yearLevelInput = document.getElementById('yearLevel');
    }
    
    bindEvents() {
        // Navigation
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => this.nextStep());
        if (this.prevBtn) this.prevBtn.addEventListener('click', () => this.prevStep());
        if (this.submitBtn) this.submitBtn.addEventListener('click', (e) => this.handleSubmit(e));
        if (this.resetBtn) this.resetBtn.addEventListener('click', () => this.resetForm());
        
        // File Upload
        if (this.idPhotoInput) {
            this.idPhotoInput.addEventListener('change', (e) => this.handleIdPhotoUpload(e));
        }
        
        // Drag and drop
        if (this.idPhotoArea) {
            this.idPhotoArea.addEventListener('dragover', (e) => this.handleDragOver(e));
            this.idPhotoArea.addEventListener('dragleave', (e) => this.handleDragLeave(e));
            this.idPhotoArea.addEventListener('drop', (e) => this.handleDrop(e));
            this.idPhotoArea.addEventListener('click', () => this.idPhotoInput.click());
        }
        
        // Camera
        if (this.captureBtn) {
            this.captureBtn.addEventListener('click', () => this.captureFace());
        }
        
        // Form inputs
        if (this.passwordInput) {
            this.passwordInput.addEventListener('input', () => this.checkPasswordStrength());
        }
        
        if (this.confirmPasswordInput) {
            this.confirmPasswordInput.addEventListener('input', () => this.validatePasswords());
        }
        
        if (this.fullNameInput) {
            this.fullNameInput.addEventListener('input', () => this.validateForm());
        }
        
        // Form validation
        if (this.form) {
            this.form.addEventListener('input', () => this.validateForm());
        }
    }
    
    // ========== STEP NAVIGATION ==========
    
    showStep(step) {
        this.currentStep = step;
        
        // Hide all sections
        this.formSections.forEach(section => section.classList.remove('active'));
        
        // Show current section
        const currentSection = document.querySelector(`[data-section="${step}"]`);
        if (currentSection) {
            currentSection.classList.add('active');
        }
        
        // Update progress
        this.updateProgress();
        
        // Update button states
        this.updateButtonStates();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    nextStep() {
        if (this.validateStep(this.currentStep)) {
            if (this.currentStep < this.totalSteps) {
                this.showStep(this.currentStep + 1);
            }
        }
    }
    
    prevStep() {
        if (this.currentStep > 1) {
            this.showStep(this.currentStep - 1);
        }
    }
    
    updateProgress() {
        this.progressSteps.forEach((step, index) => {
            const stepNumber = index + 1;
            step.classList.remove('active', 'completed');
            
            if (stepNumber < this.currentStep) {
                step.classList.add('completed');
            } else if (stepNumber === this.currentStep) {
                step.classList.add('active');
            }
        });
    }
    
    updateButtonStates() {
        // Previous button
        if (this.prevBtn) {
            this.prevBtn.style.display = this.currentStep > 1 ? 'inline-flex' : 'none';
        }
        
        // Next button
        if (this.nextBtn) {
            this.nextBtn.style.display = this.currentStep < this.totalSteps ? 'inline-flex' : 'none';
        }
        
        // Submit button
        if (this.submitBtn) {
            this.submitBtn.style.display = this.currentStep === this.totalSteps ? 'inline-flex' : 'none';
        }
    }
    
    // ========== VALIDATION ==========
    
    validateStep(step) {
        switch (step) {
            case 1:
                return this.validateStep1();
            case 2:
                return this.validateStep2();
            case 3:
                return this.validateStep3();
            case 4:
                return this.validateStep4();
            default:
                return false;
        }
    }
    
    validateStep1() {
        const errors = [];
        
        // ID Photo validation
        if (!this.idPhotoFile) {
            errors.push('Please upload your ID photo');
        }
        
        // Student ID validation
        if (!this.studentIdInput.value.trim()) {
            errors.push('Student ID is required (should be auto-detected)');
        }
        
        // ID Name validation
        if (!this.idNameInput.value.trim()) {
            errors.push('Name as it appears on ID is required');
        }
        
        if (errors.length > 0) {
            this.showAlert('Please fix the following errors:\n' + errors.join('\n'), 'error');
            return false;
        }
        
        return true;
    }
    
    validateStep2() {
        const errors = [];
        
        // Full Name
        if (!this.fullNameInput.value.trim()) {
            errors.push('Full name is required');
        }
        
        // Check name matching
        const normalizedFull = this.normalizeName(this.fullNameInput.value);
        const normalizedId = this.normalizeName(this.idNameInput.value);
        
        if (normalizedFull !== normalizedId) {
            errors.push('Full name must match the name on your ID card');
        }
        
        // Department
        if (!this.departmentInput.value.trim()) {
            errors.push('Department is required');
        }
        
        // Section
        if (!this.sectionInput.value.trim()) {
            errors.push('Section is required');
        }
        
        // Course
        if (!this.courseInput.value.trim()) {
            errors.push('Course is required');
        }
        
        if (errors.length > 0) {
            this.showAlert('Please fix the following errors:\n' + errors.join('\n'), 'error');
            return false;
        }
        
        return true;
    }
    
    validateStep3() {
        const errors = [];
        
        // Username
        if (!this.usernameInput.value.trim()) {
            errors.push('Username is required');
        } else if (this.usernameInput.value.trim().length < 4) {
            errors.push('Username must be at least 4 characters');
        }
        
        // Password
        if (!this.passwordInput.value) {
            errors.push('Password is required');
        } else if (!this.isPasswordStrong(this.passwordInput.value)) {
            errors.push('Password must be at least 8 characters with uppercase, lowercase, number, and special character');
        }
        
        // Confirm password
        if (this.passwordInput.value !== this.confirmPasswordInput.value) {
            errors.push('Passwords do not match');
        }
        
        if (errors.length > 0) {
            this.showAlert('Please fix the following errors:\n' + errors.join('\n'), 'error');
            return false;
        }
        
        return true;
    }
    
    validateStep4() {
        const errors = [];
        
        // Face descriptor
        if (!this.faceDescriptor) {
            errors.push('Facial recognition is required. Please capture your face');
        }
        
        if (errors.length > 0) {
            this.showAlert('Please fix the following errors:\n' + errors.join('\n'), 'error');
            return false;
        }
        
        return true;
    }
    
    validateForm() {
        // Implementation of real-time validation if needed
    }
    
    // ========== FILE UPLOAD ==========
    
    handleIdPhotoUpload(e) {
        const file = e.target.files[0];
        if (file) {
            this.processIdPhoto(file);
        }
    }
    
    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.idPhotoArea.classList.add('active');
    }
    
    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        this.idPhotoArea.classList.remove('active');
    }
    
    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.idPhotoArea.classList.remove('active');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            this.processIdPhoto(files[0]);
        }
    }
    
    processIdPhoto(file) {
        // Validate file type
        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
            this.showAlert('Please upload a JPG or PNG image', 'error');
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            this.showAlert('File size must be less than 5MB', 'error');
            return;
        }
        
        this.idPhotoFile = file;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.style.borderRadius = '10px';
            img.style.boxShadow = 'var(--shadow-md)';
            
            this.idPhotoPreview.innerHTML = '';
            this.idPhotoPreview.appendChild(img);
            
            const badge = document.createElement('div');
            badge.className = 'file-preview-badge';
            badge.innerHTML = '✓';
            this.idPhotoPreview.appendChild(badge);
            
            this.idPhotoArea.classList.add('active');
            
            // Trigger OCR
            this.performOCR(e.target.result);
        };
        reader.readAsDataURL(file);
    }
    
    performOCR(imageData) {
        // Show loading state
        this.showAlert('Processing ID photo with OCR...', 'info');
        
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('image', this.idPhotoFile);
        
        // Call OCR API
        fetch('../api/ocr/ocr_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Auto-fill fields from OCR
                this.studentIdInput.value = data.student_id || '';
                this.idNameInput.value = data.name || '';
                this.ocrData = data;
                this.showAlert('ID photo processed successfully! Review the extracted data.', 'success');
            } else {
                this.showAlert('Could not extract data from ID photo. Please manually enter your information.', 'warning');
            }
        })
        .catch(error => {
            console.error('OCR Error:', error);
            this.showAlert('OCR processing failed. Please manually enter your information.', 'warning');
        });
    }
    
    // ========== FACIAL RECOGNITION ==========
    
    initializeFaceDetection() {
        // Load face-api models
        const MODEL_URL = '../assets/js/models/';
        
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL)
        ])
        .then(() => {
            this.faceDetectionReady = true;
            if (this.currentStep === 4) {
                this.startCamera();
            }
        })
        .catch(error => {
            console.error('Face Detection Error:', error);
            this.showAlert('Could not load face detection models', 'error');
        });
    }
    
    startCamera() {
        if (!this.faceDetectionReady) {
            this.showAlert('Face detection is loading. Please wait...', 'warning');
            return;
        }
        
        // Request camera access
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                this.stream = stream;
                this.video.srcObject = stream;
                this.video.play();
                
                this.captureBtn.disabled = false;
                this.captureBtn.innerText = '📷 Capture Face';
                this.updateFaceStatus('Camera ready. Position your face and click "Capture Face"', 'loading');
            })
            .catch(error => {
                console.error('Camera Error:', error);
                this.showAlert('Unable to access camera. Check permissions.', 'error');
                this.updateFaceStatus('Camera access denied', 'error');
            });
    }
    
    captureFace() {
        if (!this.video || !this.video.srcObject) {
            this.showAlert('Camera is not ready', 'error');
            return;
        }
        
        // Draw video frame to canvas
        const ctx = this.canvas.getContext('2d');
        ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        
        // Detect faces
        this.detectFace()
            .then(detections => {
                if (detections && detections.length > 0) {
                    this.faceDescriptor = JSON.stringify(detections[0].descriptor);
                    this.updateFaceStatus('✓ Face captured successfully!', 'success');
                    this.showAlert('Face captured successfully!', 'success');
                    this.captureBtn.innerText = '✓ Face Captured';
                    this.captureBtn.disabled = true;
                    
                    // Stop camera
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                } else {
                    this.updateFaceStatus('No face detected. Please try again.', 'error');
                    this.showAlert('No face detected. Please position your face clearly in the camera.', 'error');
                }
            })
            .catch(error => {
                console.error('Face Detection Error:', error);
                this.updateFaceStatus('Error detecting face', 'error');
            });
    }
    
    detectFace() {
        return faceapi
            .detectAllFaces(this.canvas, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptors();
    }
    
    updateFaceStatus(message, status) {
        if (this.faceStatus) {
            this.faceStatus.innerHTML = message;
            this.faceStatus.className = `face-status ${status}`;
        }
    }
    
    // ========== PASSWORD VALIDATION ==========
    
    isPasswordStrong(password) {
        return password.length >= 8 &&
               /[A-Z]/.test(password) &&
               /[a-z]/.test(password) &&
               /[0-9]/.test(password) &&
               /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    }
    
    checkPasswordStrength() {
        const password = this.passwordInput.value;
        const strengthContainer = document.getElementById('passwordStrength');
        
        if (!strengthContainer) return;
        
        let strength = 0;
        let requirements = [];
        
        if (password.length >= 8) strength += 25;
        else requirements.push('At least 8 characters');
        
        if (/[A-Z]/.test(password)) strength += 25;
        else requirements.push('Uppercase letter');
        
        if (/[a-z]/.test(password)) strength += 25;
        else requirements.push('Lowercase letter');
        
        if (/[0-9]/.test(password)) strength += 12.5;
        else requirements.push('Number');
        
        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength += 12.5;
        else requirements.push('Special character');
        
        // Update strength meter
        const strengthBar = strengthContainer.querySelector('.strength-bar');
        const strengthText = strengthContainer.querySelector('.strength-text');
        
        if (strengthBar) {
            strengthBar.classList.remove('weak', 'fair', 'strong');
            
            if (strength < 50) {
                strengthBar.classList.add('weak');
                strengthText.textContent = '❌ Weak';
                strengthText.classList.remove('fair', 'strong');
                strengthText.classList.add('weak');
            } else if (strength < 80) {
                strengthBar.classList.add('fair');
                strengthText.textContent = '⚠️ Fair';
                strengthText.classList.remove('weak', 'strong');
                strengthText.classList.add('fair');
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = '✓ Strong';
                strengthText.classList.remove('weak', 'fair');
                strengthText.classList.add('strong');
            }
            
            strengthBar.style.width = strength + '%';
        }
        
        this.validatePasswords();
    }
    
    validatePasswords() {
        if (this.passwordInput.value !== this.confirmPasswordInput.value) {
            this.confirmPasswordInput.parentElement.classList.add('error');
        } else {
            this.confirmPasswordInput.parentElement.classList.remove('error');
        }
    }
    
    // ========== FORM SUBMISSION ==========
    
    handleSubmit(e) {
        e.preventDefault();
        
        if (!this.validateStep(this.currentStep)) {
            return;
        }
        
        // Collect form data
        const formData = new FormData(this.form);
        formData.append('face_descriptor', this.faceDescriptor);
        if (this.ocrData) {
            formData.append('ocr_data', JSON.stringify(this.ocrData));
        }
        
        // Show loading state
        this.submitBtn.disabled = true;
        const originalText = this.submitBtn.innerText;
        this.submitBtn.innerHTML = '<span class="spinner"></span> Registering...';
        
        // Submit form
        fetch(this.form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Parse response for success/error
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const successMsg = doc.querySelector('.alert-success');
            const errorMsg = doc.querySelector('.alert-error');
            
            if (successMsg) {
                this.showAlert('Registration successful! Redirecting to login...', 'success');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else if (errorMsg) {
                this.showAlert(errorMsg.textContent, 'error');
                this.submitBtn.disabled = false;
                this.submitBtn.innerText = originalText;
            }
        })
        .catch(error => {
            console.error('Submission Error:', error);
            this.showAlert('An error occurred during registration. Please try again.', 'error');
            this.submitBtn.disabled = false;
            this.submitBtn.innerText = originalText;
        });
    }
    
    resetForm() {
        if (confirm('Are you sure you want to clear all fields?')) {
            this.form.reset();
            this.idPhotoFile = null;
            this.faceDescriptor = null;
            this.ocrData = null;
            this.idPhotoPreview.innerHTML = '';
            this.idPhotoArea.classList.remove('active');
            this.showStep(1);
            this.showAlert('Form cleared', 'info');
        }
    }
    
    // ========== UTILITIES ==========
    
    normalizeName(name) {
        return name.trim()
                   .replace(/\s+/g, ' ')
                   .toLowerCase();
    }
    
    showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        
        const alertId = 'alert-' + Date.now();
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.id = alertId;
        alert.innerHTML = `
            <span class="alert-icon">${icons[type]}</span>
            <div class="alert-content">${message}</div>
            <button class="alert-close" onclick="document.getElementById('${alertId}').remove()">×</button>
        `;
        
        alertContainer.appendChild(alert);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const element = document.getElementById(alertId);
            if (element) element.remove();
        }, 5000);
    }
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', () => {
    window.registration = new StudentRegistration();
});
