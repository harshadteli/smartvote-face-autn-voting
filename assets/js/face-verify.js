/**
 * face-verify.js — Biometric Face Verification for Voting
 * Uses face-api.js to compare live camera feed against registered profile picture.
 * E-Voting System v1.2
 */

const FaceVerify = {
    modelPath: 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights',
    modelsLoaded: false,
    referenceDescriptor: null,
    verificationPassed: false,
    stream: null,

    async loadModels() {
        try {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(this.modelPath),
                faceapi.nets.faceLandmark68Net.loadFromUri(this.modelPath),
                faceapi.nets.faceRecognitionNet.loadFromUri(this.modelPath),
            ]);
            this.modelsLoaded = true;
            console.log('[FaceVerify] Models loaded.');
            return true;
        } catch (e) {
            console.error('[FaceVerify] Model load error:', e);
            return false;
        }
    },

    /**
     * Load the voter's profile picture from a URL, compute its face descriptor.
     * @param {string} imageUrl — relative or absolute URL to the stored profile picture
     */
    async loadReferenceImage(imageUrl) {
        try {
            const img = await faceapi.fetchImage(imageUrl);
            const detection = await faceapi
                .detectSingleFace(img)
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                return { success: false, message: 'No face detected in your profile picture. Please contact support.' };
            }
            this.referenceDescriptor = detection.descriptor;
            return { success: true };
        } catch (e) {
            console.error('[FaceVerify] Reference image error:', e);
            return { success: false, message: 'Could not process your profile picture.' };
        }
    },

    /**
     * Open the camera, show in videoEl, and scan.
     * Returns true if face matches, false otherwise.
     * @param {HTMLVideoElement} videoEl
     * @param {HTMLCanvasElement} canvasEl
     * @param {Function} onStatusUpdate — callback(message, type) where type: 'info'|'success'|'error'
     */
    async startVerification(videoEl, canvasEl, onStatusUpdate) {
        if (!this.modelsLoaded) {
            onStatusUpdate('Loading AI models, please wait...', 'info');
            const ok = await this.loadModels();
            if (!ok) {
                onStatusUpdate('Failed to load face recognition models.', 'error');
                return false;
            }
        }

        onStatusUpdate('Opening camera...', 'info');
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            videoEl.srcObject = this.stream;
            await videoEl.play();
        } catch (e) {
            onStatusUpdate('Camera access denied. Please allow camera permission.', 'error');
            return false;
        }

        onStatusUpdate('Position your face in the frame...', 'info');

        // Attempt scanning for up to 15 seconds
        const startTime = Date.now();
        const TIMEOUT_MS = 15000;
        const MATCH_THRESHOLD = 0.5; // Lower = stricter match

        return new Promise((resolve) => {
            const scanInterval = setInterval(async () => {
                if (Date.now() - startTime > TIMEOUT_MS) {
                    clearInterval(scanInterval);
                    this.stopCamera();
                    onStatusUpdate('Timeout: No matching face detected. Please try again.', 'error');
                    resolve(false);
                    return;
                }

                const detection = await faceapi
                    .detectSingleFace(videoEl, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection) return; // Keep trying

                // Draw detection box on canvas overlay
                const dims = faceapi.matchDimensions(canvasEl, videoEl, true);
                const resized = faceapi.resizeResults(detection, dims);
                canvasEl.getContext('2d').clearRect(0, 0, canvasEl.width, canvasEl.height);
                faceapi.draw.drawDetections(canvasEl, resized);

                const distance = faceapi.euclideanDistance(detection.descriptor, this.referenceDescriptor);
                console.log('[FaceVerify] Distance:', distance.toFixed(4));

                if (distance < MATCH_THRESHOLD) {
                    clearInterval(scanInterval);
                    this.stopCamera();
                    this.verificationPassed = true;
                    onStatusUpdate('Identity Verified ✓', 'success');
                    resolve(true);
                } else {
                    onStatusUpdate(`Scanning... (match score: ${(1 - distance).toFixed(2)})`, 'info');
                }
            }, 500);
        });
    },

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
    }
};

// Sync canvas size to video element on resize (responsive)
window.addEventListener('resize', () => {
    const v = document.getElementById('face-video');
    const c = document.getElementById('face-canvas');
    if (v && c) {
        c.width = v.clientWidth;
        c.height = v.clientHeight;
    }
});
