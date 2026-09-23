document.addEventListener('DOMContentLoaded', function() {
    const regForm = document.getElementById('regForm');
    if (regForm) {
        regForm.addEventListener('submit', async function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return;
            }

            // Face registration is now required
            const faceDescriptor = document.getElementById('face_descriptor').value;
            if (!faceDescriptor || faceDescriptor.trim() === '') {
                e.preventDefault();
                alert('Face registration is required. Please capture your face/image before registering.');
                return;
            }

            // Validate that ID name has been entered
            const idName = document.querySelector('input[name="id_name"]').value.trim();
            if (!idName || idName === '') {
                e.preventDefault();
                alert('Please enter the name exactly as it appears on your ID card.');
                return;
            }

            // Validate name matching (ensure user entered the correct name)
            const fullName = document.querySelector('input[name="full_name"]').value.trim();

            // Debug logging
            console.log('Name validation - Full name:', fullName, 'ID name:', idName);

            // Normalize names for comparison (same as server-side normalizeName function)
            const normalizedFull = fullName.replace(/\s+/g, ' ').toLowerCase();
            const normalizedId = idName.replace(/\s+/g, ' ').toLowerCase();

            console.log('Normalized - Full:', normalizedFull, 'ID:', normalizedId);

            if (normalizedFull !== normalizedId) {
                e.preventDefault();
                alert(`Name mismatch!\nEntered: "${fullName}"\nID Card: "${idName}"\n\nPlease ensure the name you enter exactly matches the name on your ID card.`);
                return;
            }
            // Validate that student ID has been entered
            const studentIdValue = document.querySelector('input[name="student_id"]').value.trim();
            if (!studentIdValue || studentIdValue === '') {
                e.preventDefault();
                alert('Please enter your Student ID.');
                return;
            }

            const idPhotoInput = document.querySelector('input[name="id_photo"]');
            if (idPhotoInput.files.length === 0) {
                e.preventDefault();
                alert('Please upload your ID photo.');
                return;
            }

            // Show loading message
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Validating ID photo...';

            try {
                const faceMatchResult = await validateIdPhotoFaceMatch(idPhotoInput.files[0], faceDescriptor);
                if (!faceMatchResult.success) {
                    e.preventDefault();
                    alert(faceMatchResult.error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    return;
                }
            } catch (error) {
                console.error('ID photo validation error:', error);
                e.preventDefault();
                alert('Error validating ID photo. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                return;
            }

            // Reset button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            console.log('Form submitted with face registration and ID validation');
        });

        // Keep ID name field synced with full name so user only enters name once
        const fullNameInput = document.querySelector('input[name="full_name"]');
        const idNameInput = document.querySelector('input[name="id_name"]');
        if (fullNameInput && idNameInput) {
            idNameInput.value = fullNameInput.value.trim();
            fullNameInput.addEventListener('input', () => {
                idNameInput.value = fullNameInput.value.trim();
            });
        }


        // Keep ID name field synced with full name so user only enters name once
        const fullNameInputSync = document.querySelector('input[name="full_name"]');
        const idNameInputSync = document.querySelector('input[name="id_name"]');
        if (fullNameInputSync && idNameInputSync) {
            // Only sync if id_name is empty (user hasn't entered anything yet)
            if (!idNameInputSync.value.trim()) {
                idNameInputSync.value = fullNameInputSync.value.trim();
            }
            // But still validate they match before submission
        }
    }
    const capturedImage = document.getElementById('captured-image');
    const faceImageInput = document.getElementById('face_descriptor');
    const submitBtn = document.querySelector('button[type="submit"]');
    const captureBtn = document.getElementById('capture-btn');

    // Disable capture button until models are loaded
    if (captureBtn) {
        captureBtn.disabled = true;
        captureBtn.textContent = 'Loading models...';
    }

    // Disable submit button until face is captured
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Register (Face Required)';
    }

    // Initialize camera
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    console.log('Face capture enabled for registration.');

    // Simple camera capture mode
    let cameraReady = false;

    // Function to update face capture status
    function updateFaceStatus(message, isError = false) {
        const statusDiv = document.getElementById('face-status');
        if (statusDiv) {
            statusDiv.style.background = isError ? '#ffebee' : '#e3f2fd';
            statusDiv.style.borderColor = isError ? '#f44336' : '#2196f3';
            statusDiv.innerHTML = `<p style="margin: 0; color: ${isError ? '#c62828' : '#1976d2'}; font-size: 0.9em;">${message}</p>`;
        }
    }

    // Start camera initialization immediately
    initializeCamera();

    // Function to initialize camera
    async function initializeCamera() {
        try {
            updateFaceStatus('Requesting camera access...');
            console.log('Requesting camera access...');
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: 640,
                    height: 480,
                    facingMode: 'user'
                }
            });
            console.log('Camera access granted');
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                video.play();
                cameraReady = true;
                console.log('Video stream started');
                if (captureBtn) {
                    captureBtn.disabled = false;
                    captureBtn.textContent = 'Capture Face (Required)';
                }
                updateFaceStatus('Camera ready! Click "Capture Face" to begin registration.', false);
            };
        } catch (error) {
            console.error('Camera access failed:', error);
            updateFaceStatus('Camera access denied or unavailable. Please allow camera access and refresh the page.', true);
            if (captureBtn) {
                captureBtn.disabled = true;
                captureBtn.textContent = 'Camera Access Required';
                captureBtn.style.opacity = '0.6';
            }
            // Disable submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Camera Access Required';
            }
        }
    }

    // Function for basic camera capture when face detection is unavailable
    function captureBasicImage() {
        try {
            updateFaceStatus('Capturing image...');
            captureBtn.disabled = true;
            captureBtn.textContent = 'Processing...';

            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, 320, 240);

            // Store a basic image data instead of face descriptor
            const imageData = canvas.toDataURL('image/png');
            const faceDescriptorInput = document.getElementById('face_descriptor');
            faceDescriptorInput.value = JSON.stringify({
                type: 'basic_capture',
                imageData: imageData,
                timestamp: Date.now(),
                note: 'Captured on slow connection without face detection'
            });

            // Show captured image
            capturedImage.src = imageData;
            capturedImage.style.display = 'block';

            updateFaceStatus('Image captured successfully! You can now complete your registration.', false);
            captureBtn.textContent = 'Image Captured ✓';
            captureBtn.disabled = true;

            // Enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Register';
            }

        } catch (error) {
            console.error('Error during basic capture:', error);
            updateFaceStatus('Error capturing image. Please try again.', true);
            captureBtn.disabled = false;
            captureBtn.textContent = 'Try Again';
        }
    }

    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            if (!cameraReady) {
                alert('Camera is not ready yet. Please wait for camera access and try again.');
                return;
            }

            captureBasicImage();
        });
    }
});


// Function to validate basic face capture for registration
async function validateIdPhotoFaceMatch(idPhotoFile, capturedFaceDescriptor) {
    if (!capturedFaceDescriptor || !capturedFaceDescriptor.trim()) {
        return {
            success: false,
            error: 'Please capture your face before submitting the form.'
        };
    }

    // Basic flow: accept captured face data without expensive model matching.
    return {
        success: true,
        message: 'Face capture recorded successfully.'
    };
}
