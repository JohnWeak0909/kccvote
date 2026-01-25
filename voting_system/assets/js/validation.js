document.addEventListener('DOMContentLoaded', function() {
    const regForm = document.getElementById('regForm');
    if (regForm) {
        regForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return;
            }
            // Face detection is now completely optional - allow form submission regardless
            console.log('Form submitted - face detection is optional');
        });
    }

    const captureBtn = document.getElementById('capture-btn');
    const capturedImage = document.getElementById('captured-image');
    const faceImageInput = document.getElementById('face_descriptor');

    // Disable capture button until models are loaded
    captureBtn.disabled = true;
    captureBtn.textContent = 'Loading models...';

    // Face detection is disabled, so skip camera initialization
    /*
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                console.log("An error occurred: " + err);
                alert("Camera access denied or not available. Please allow camera access to capture your face.");
            });
    } else {
        alert("Camera not supported on this device.");
    }
    */

    console.log('Face detection is disabled for registration. Camera initialization skipped.');

    // Improved face detection with local fallback
    let modelsLoaded = false;

    // Function to update face detection status
    function updateFaceStatus(message, isError = false) {
        const statusDiv = document.getElementById('face-status');
        if (statusDiv) {
            statusDiv.style.background = isError ? '#ffebee' : '#e3f2fd';
            statusDiv.style.borderColor = isError ? '#f44336' : '#2196f3';
            statusDiv.innerHTML = `<p style="margin: 0; color: ${isError ? '#c62828' : '#1976d2'}; font-size: 0.9em;">${message}</p>`;
        }
    }

    // Try to load models with multiple fallbacks
    async function loadFaceModels() {
        try {
            updateFaceStatus('Loading face detection models from local files...');
            // First try: Load from local directory
            console.log('Attempting to load face models from local directory...');
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('assets/js/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('assets/js/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('assets/js/models')
            ]);
            console.log('Face models loaded successfully from local directory');
            modelsLoaded = true;
            captureBtn.disabled = false;
            captureBtn.textContent = 'Capture Face';
            updateFaceStatus('Face detection ready! Click "Capture Face" to begin.', false);
        } catch (localError) {
            console.warn('Local models not available:', localError.message);
            updateFaceStatus('Local models not found, trying online sources...');
            try {
                // Second try: Load from alternative CDN
                console.log('Attempting to load face models from alternative CDN...');
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('https://unpkg.com/face-api.js@0.22.2/weights'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('https://unpkg.com/face-api.js@0.22.2/weights'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('https://unpkg.com/face-api.js@0.22.2/weights')
                ]);
                console.log('Face models loaded successfully from alternative CDN');
                modelsLoaded = true;
                captureBtn.disabled = false;
                captureBtn.textContent = 'Capture Face';
                updateFaceStatus('Face detection ready! Click "Capture Face" to begin.', false);
            } catch (cdnError) {
                console.warn('CDN models failed to load:', cdnError.message);
                updateFaceStatus('Online models unavailable, enabling basic face detection...');
                // Third try: Minimal face detection with just tiny face detector
                try {
                    console.log('Attempting minimal face detection...');
                    await faceapi.nets.tinyFaceDetector.loadFromUri('assets/js/models');
                    console.log('Minimal face detection enabled (basic face detection only)');
                    modelsLoaded = true;
                    captureBtn.disabled = false;
                    captureBtn.textContent = 'Basic Face Capture';
                    updateFaceStatus('Basic face detection enabled. Registration will proceed with limited facial recognition.', false);
                } catch (minimalError) {
                    console.error('All face detection methods failed:', minimalError.message);
                    captureBtn.disabled = true;
                    captureBtn.textContent = 'Face detection unavailable';
                    captureBtn.style.opacity = '0.6';
                    updateFaceStatus('Face detection is currently unavailable. You can still register without facial recognition.', true);
                }
            }
        }
    }

    // Start loading models
    loadFaceModels();

    captureBtn.addEventListener('click', async function() {
        alert('Face detection is currently disabled. You can register without facial recognition.');
        return;
    });
    // Rest of the face detection code is commented out
    /*
