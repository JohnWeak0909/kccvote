<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Detection Test - KCC Online Voting</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        video, canvas { border: 1px solid #ccc; margin: 10px 0; }
        button { padding: 10px 20px; margin: 5px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Face Detection Test</h1>
        <div id="status" class="status info">Initializing face detection...</div>

        <div id="camera-container">
            <video id="video" width="320" height="240" autoplay muted></video>
            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
            <br>
            <button id="capture-btn" disabled>Test Face Detection</button>
            <button id="stop-btn" style="display:none;">Stop Camera</button>
        </div>

        <div id="results" style="margin-top: 20px;"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture-btn');
        const stopBtn = document.getElementById('stop-btn');
        const statusDiv = document.getElementById('status');
        const resultsDiv = document.getElementById('results');
        let modelsLoaded = false;
        let stream = null;

        function updateStatus(message, type = 'info') {
            statusDiv.className = `status ${type}`;
            statusDiv.textContent = message;
        }

        async function loadFaceModels() {
            try {
                updateStatus('Loading face detection models from local files...');
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('../assets/js/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('../assets/js/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('../assets/js/models')
                ]);
                modelsLoaded = true;
                captureBtn.disabled = false;
                updateStatus('Face detection models loaded successfully!', 'success');
            } catch (error) {
                console.error('Error loading models:', error);
                updateStatus('Failed to load face detection models: ' + error.message, 'error');
            }
        }

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                updateStatus('Camera started. Click "Test Face Detection" to begin.', 'success');
            } catch (error) {
                updateStatus('Camera access denied or unavailable: ' + error.message, 'error');
            }
        }

        async function testFaceDetection() {
            if (!modelsLoaded) {
                updateStatus('Models not loaded yet', 'error');
                return;
            }

            try {
                updateStatus('Detecting faces...', 'info');
                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                if (detections.length > 0) {
                    updateStatus(`Found ${detections.length} face(s)! Face detection is working.`, 'success');
                    resultsDiv.innerHTML = `
                        <h3>Detection Results:</h3>
                        <p>Faces detected: ${detections.length}</p>
                        <p>Face descriptor length: ${detections[0].descriptor.length}</p>
                        <p>Test completed successfully!</p>
                    `;
                } else {
                    updateStatus('No faces detected. Make sure your face is visible in the camera.', 'error');
                    resultsDiv.innerHTML = '<p>No faces detected in the current frame.</p>';
                }
            } catch (error) {
                updateStatus('Face detection error: ' + error.message, 'error');
                resultsDiv.innerHTML = '<p>Error during face detection: ' + error.message + '</p>';
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            updateStatus('Camera stopped', 'info');
            stopBtn.style.display = 'none';
            captureBtn.style.display = 'inline-block';
        }

        // Event listeners
        captureBtn.addEventListener('click', async () => {
            if (!stream) {
                await startCamera();
                captureBtn.textContent = 'Test Face Detection';
                stopBtn.style.display = 'inline-block';
            } else {
                await testFaceDetection();
            }
        });

        stopBtn.addEventListener('click', stopCamera);

        // Initialize
        loadFaceModels();
    </script>
</body>
</html>