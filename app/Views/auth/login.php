<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KEVS (KCC e-Voting System)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <style>
        /* Form wrapper (original) */
        body.dashboard-page .container > .form-wrapper {
            width: 344px !important;
            max-width: 344px !important;
            min-height: 620px !important;
            padding: 1.4rem 1rem !important;
            margin: 2rem auto !important;
        }
        body.dashboard-page .container > .form-wrapper .logo {
            width: 68px !important;
            height: 68px !important;
        }
        body.dashboard-page .container > .form-wrapper .main-title {
            font-size: 1.7rem !important;
        }
        body.dashboard-page .container > .form-wrapper .subtitle {
            font-size: 0.95rem !important;
        }
        body.dashboard-page .container > .form-wrapper .btn-admin {
            padding: 0.95rem 1.4rem !important;
            font-size: 1rem !important;
        }

        /* Premium Fintech Face Verification Styles */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 30px rgba(6, 182, 212, 0.4), inset 0 0 30px rgba(6, 182, 212, 0.1);
                border-width: 2px;
            }
            50% {
                box-shadow: 0 0 50px rgba(6, 182, 212, 0.6), inset 0 0 40px rgba(6, 182, 212, 0.15);
                border-width: 2.5px;
            }
        }
        
        @keyframes scanLine {
            0% {
                top: -100%;
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                top: 100%;
                opacity: 0;
            }
        }
        
        @keyframes pulse-dot {
            0%, 100% {
                background: rgba(6, 182, 212, 0.4);
                transform: scale(1);
            }
            50% {
                background: rgba(6, 182, 212, 0.8);
                transform: scale(1.2);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes drawCheck {
            from {
                stroke-dasharray: 42;
                stroke-dashoffset: 42;
                opacity: 0;
            }
            to {
                stroke-dasharray: 42;
                stroke-dashoffset: 0;
                opacity: 1;
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Face verification inside form-wrapper */
        body.dashboard-page .form-wrapper.face-verify-mode {
            width: 100% !important;
            max-width: 420px !important;
            min-height: auto !important;
            padding: 1.4rem 1rem 1.6rem !important;
        }

        #faceVerificationContainer {
            position: relative;
            width: 100%;
            display: none;
            flex-direction: column;
            align-items: center;
            margin-top: 0.5rem;
            padding: 0;
            box-sizing: border-box;
        }

        #faceVerificationContainer.is-active {
            display: flex;
        }

        #faceVerificationContainer .face-verify-bg {
            display: none;
        }

        #faceVerificationContainer .face-verify-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        #faceVerificationContainer .face-verify-title {
            color: #2D4A73;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            text-align: center;
        }

        #faceVerificationContainer .face-verify-subtitle {
            color: #64748b;
            font-size: 0.8rem;
            margin: 0 0 1rem 0;
            text-align: center;
        }

        #faceVerificationContainer #mainStatus {
            color: #2D4A73 !important;
            font-size: 1rem !important;
        }

        #faceVerificationContainer #subStatus {
            color: #64748b !important;
        }

        #faceVerificationContainer .face-frame-wrap {
            position: relative;
            width: 220px;
            height: 268px;
            margin-bottom: 1rem;
        }

        #faceVerificationContainer #glowRing {
            width: 220px !important;
            height: 268px !important;
            border-radius: 28px !important;
        }

        #faceVerificationContainer #scannerAnimation {
            border-radius: 28px !important;
        }

        #faceVerificationContainer #video {
            border-radius: 28px !important;
        }

        #faceVerificationContainer .trust-badges {
            border-top-color: rgba(45, 74, 115, 0.15) !important;
        }

        #faceVerificationContainer .trust-badges span {
            color: #64748b !important;
        }

        #faceVerificationContainer .trust-badges svg {
            stroke: #2D4A73;
        }

        #faceVerificationContainer #cancelBtn {
            background: rgba(45, 74, 115, 0.08) !important;
            color: #2D4A73 !important;
            border-color: rgba(45, 74, 115, 0.2) !important;
        }

        @media (max-width: 480px) {
            #faceVerificationContainer .face-frame-wrap {
                width: 200px;
                height: 244px;
            }

            #faceVerificationContainer #glowRing {
                width: 200px !important;
                height: 244px !important;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="<?= base_url('js/main.js') ?>" defer></script>
</head>
<body class="dashboard-page">
    <div class="container">
        <?php $faceVerify = ($action === 'student' && isset($verify) && $verify === 'face'); ?>
        <div class="form-wrapper<?= $faceVerify ? ' face-verify-mode' : '' ?>">
            <div class="logo-container">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <img src="<?= base_url('images/kcclogo.png') ?>" alt="KEVS Logo" style="width: 80px; height: 80px; display: inline-block; filter: drop-shadow(0 4px 12px rgba(252, 211, 77, 0.2));">
                </div>
                <h1 class="main-title">KEVS</h1>
                <p class="subtitle"><?php echo ($action === 'admin') ? 'Admin Portal' : 'Student Portal'; ?></p>
            </div>
            <h2><?php echo $faceVerify ? 'Verify Your Identity' : (($action === 'admin') ? 'Admin Login' : 'Student Login'); ?></h2>
            <?php if ($faceVerify): ?>
            <p class="face-verify-page-sub" style="text-align:center;color:#64748b;font-size:0.85rem;margin:-0.5rem 0 0.75rem;">Biometric liveness detection</p>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <?php if (!$faceVerify): ?>
                <style>
                    /* KEVS Login Theme */
                    .kevs-card { background: #0B2748; border-radius: 16px; padding: 20px; border: 1px solid #24476B; box-shadow: 0 20px 60px rgba(2,10,30,0.6); }
                    .kevs-input { background: #102F52; border: 1px solid #24476B; color: #FFFFFF; padding: 12px 14px; width: 100%; border-radius: 8px; font-size: 14px; }
                    .password-field { position: relative; }
                    .password-field .kevs-input { padding-left: 48px; }
                    .kevs-label { color: #F5C542; font-weight: 600; margin-bottom: 6px; display:block; }
                    .kevs-hint { color: #B8C7D9; font-size: 0.9rem; margin-top:8px; }
                    .kevs-btn { background: linear-gradient(90deg, #1266D6 0%, #1677FF 100%); color:#fff; border:none; padding:12px 18px; border-radius:10px; font-weight:700; }
                    .kevs-scan { background: #F5C542; color:#061A33; border:none; padding:12px 18px; border-radius:10px; font-weight:700; }
                    .kevs-or { display:flex;align-items:center;gap:12px;margin:16px 0;color:#B8C7D9 }
                    .kevs-or hr{flex:1;border:none;height:1px;background:#24476B}
                    .password-control { position:relative; }
                    .password-control .kevs-input { width:100%; padding-right:48px; }
                    .password-control .eye-btn{position:absolute;left:auto !important;right:0 !important;top:0;width:42px !important;height:100%;display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#B8C7D9;cursor:pointer;padding:6px;min-width:0}
                </style>

                <div class="kevs-card">
                    <?php if ($action === 'admin'): ?>
                        <form id="adminLoginForm" action="<?= base_url('login/attempt?action=' . htmlspecialchars($action)) ?>" method="POST">
                            <div style="margin-bottom:12px">
                                <label class="kevs-label">Username</label>
                                <input class="kevs-input" type="text" name="username" id="loginUsername" placeholder="Enter your username" required autofocus />
                            </div>

                            <div class="password-field" style="margin-bottom:12px;">
                                <label class="kevs-label">Password</label>
                                <div class="password-control">
                                    <input class="kevs-input" type="password" name="password" id="loginPassword" placeholder="Enter your password" required />
                                    <button type="button" id="toggleLoginPassword" class="eye-btn">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div style="display:flex;gap:10px;margin-top:14px;">
                                <button type="submit" class="kevs-btn" style="flex:1;">Admin Login</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <form id="studentLoginForm" action="<?= base_url('login/attempt?action=' . htmlspecialchars($action)) ?>" method="POST">
                                <div style="margin-bottom:12px">
                                    <label class="kevs-label">Student ID Number</label>
                                    <input class="kevs-input" type="text" name="username" id="loginStudentId" placeholder="Enter your student ID number" required autofocus inputmode="numeric" pattern="\d*" />
                                </div>

                            <div class="password-field" style="margin-bottom:12px;">
                                <label class="kevs-label">Password</label>
                                <div class="password-control">
                                    <input class="kevs-input" type="password" name="password" id="loginPassword" placeholder="Enter your password" required />
                                    <button type="button" id="toggleLoginPassword" class="eye-btn">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <div id="locationStatus" class="mt-2 small text-muted">Checking campus location...</div>

                            <div style="display:flex;gap:10px;margin-top:14px;">
                                <button type="submit" class="kevs-btn" style="flex:1;">Student Login</button>
                            </div>
                        </form>

                        <div class="kevs-or"><hr/><div>OR</div><hr/></div>

                        <!-- Face start form posts student_id to server to initiate face verify session -->
                        <form id="startFaceForm" action="<?= base_url('login/face-start') ?>" method="POST">
                            <input type="hidden" name="student_id" id="faceStudentId" />
                            <div style="display:flex;gap:10px;align-items:center;">
                                <button type="button" id="scanFaceBtn" class="kevs-scan" style="flex:1;"><i class="bi bi-camera-video-fill" style="margin-right:8px"></i> Scan Face</button>
                            </div>
                        </form>

                        <div class="kevs-hint" style="margin-top:12px">Face verification is only available for accounts with face registration.</div>
                    <?php endif; ?>
                    <div style="margin-top:12px;text-align:center;color:#B8C7D9;font-size:0.95rem">
                        <?php if ($action === 'admin'): ?>
                            <a href="<?= base_url('login?action=student') ?>" style="color:#F5C542;">Student Login</a>
                        <?php else: ?>
                            <a href="<?= base_url('login?action=admin') ?>" style="color:#F5C542;">Admin Login</a>
                        <?php endif; ?>
                        <div style="margin-top:10px;color:#64748b;font-size:0.9rem">Student accounts are created by the admin only.</div>
                        <div style="margin-top:8px"><a href="<?= base_url('/') ?>" style="color:#F5C542;text-decoration:underline;font-weight:600">← Back to Home</a></div>
                    </div>
                </div>

                <script>
                    // Toggle password visibility
                    const toggleBtn = document.getElementById('toggleLoginPassword');
                    if (toggleBtn) toggleBtn.addEventListener('click', ()=>{
                        const pwd = document.getElementById('loginPassword');
                        const icon = toggleBtn.querySelector('i');
                        if (!pwd) return;
                        if (pwd.type === 'password') { pwd.type = 'text'; if (icon) { icon.className='bi bi-eye-slash'; } }
                        else { pwd.type = 'password'; if (icon) { icon.className='bi bi-eye'; } }
                    });

                    // Handle Scan Face submit (student login only)
                    const scanBtn = document.getElementById('scanFaceBtn');
                    if (scanBtn) scanBtn.addEventListener('click', ()=>{
                        const sidEl = document.getElementById('loginStudentId');
                        const sid = (sidEl ? sidEl.value : '').toString().trim();
                        if (!sid) { alert('Please enter your Student ID first.'); return; }
                        const faceInput = document.getElementById('faceStudentId');
                        if (faceInput) {
                            faceInput.value = sid;
                            const startForm = document.getElementById('startFaceForm');
                            if (startForm) startForm.submit();
                        }
                    });

                    // Enforce digits-only for Student ID (prevent letters)
                    const studentIdEl = document.getElementById('loginStudentId');
                    if (studentIdEl) {
                        studentIdEl.setAttribute('inputmode', 'numeric');
                        studentIdEl.setAttribute('pattern', '\\d*');
                        studentIdEl.addEventListener('input', (e) => {
                            const cleaned = e.target.value.replace(/\D+/g, '');
                            if (e.target.value !== cleaned) e.target.value = cleaned;
                        });
                        studentIdEl.addEventListener('paste', (e) => {
                            const paste = (e.clipboardData || window.clipboardData).getData('text') || '';
                            if (/\D/.test(paste)) {
                                e.preventDefault();
                                const cleaned = paste.replace(/\D+/g, '');
                                document.execCommand('insertText', false, cleaned);
                            }
                        });
                    }
                </script>
            <?php endif; ?>

            <?php if ($faceVerify): ?>
                <div id="faceVerificationContainer" class="face-verification-panel is-active">
                    <div class="face-verify-inner">
                        <div class="face-frame-wrap">
                            <!-- Outer Glow Ring -->
                            <div id="glowRing" style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 280px;
                                height: 340px;
                                border-radius: 40px;
                                border: 2px solid;
                                border-image: linear-gradient(135deg, #06b6d4 0%, #22c55e 100%) 1;
                                box-shadow: 0 0 30px rgba(6, 182, 212, 0.4),
                                           inset 0 0 30px rgba(6, 182, 212, 0.1);
                                animation: pulseGlow 3s ease-in-out infinite;
                            "></div>

                            <!-- Scanning Lines Animation -->
                            <div id="scannerAnimation" style="
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                border-radius: 40px;
                                overflow: hidden;
                                opacity: 0.6;
                            ">
                                <div style="
                                    position: absolute;
                                    top: -100%;
                                    left: 0;
                                    width: 100%;
                                    height: 2px;
                                    background: linear-gradient(180deg, transparent 0%, rgba(6, 182, 212, 0.8) 50%, transparent 100%);
                                    box-shadow: 0 0 20px rgba(6, 182, 212, 0.6);
                                    animation: scanLine 3s ease-in-out infinite;
                                "></div>
                            </div>

                            <!-- Face Circles (Corner Markers) -->
                            <div style="
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 20px;
                                height: 20px;
                                border: 2px solid #06b6d4;
                                border-right: none;
                                border-bottom: none;
                                border-radius: 0 0 0 0;
                            "></div>
                            <div style="
                                position: absolute;
                                top: 0;
                                right: 0;
                                width: 20px;
                                height: 20px;
                                border: 2px solid #06b6d4;
                                border-left: none;
                                border-bottom: none;
                                border-radius: 0 0 0 0;
                            "></div>
                            <div style="
                                position: absolute;
                                bottom: 0;
                                left: 0;
                                width: 20px;
                                height: 20px;
                                border: 2px solid #22c55e;
                                border-right: none;
                                border-top: none;
                                border-radius: 0 0 0 0;
                            "></div>
                            <div style="
                                position: absolute;
                                bottom: 0;
                                right: 0;
                                width: 20px;
                                height: 20px;
                                border: 2px solid #22c55e;
                                border-left: none;
                                border-top: none;
                                border-radius: 0 0 0 0;
                            "></div>

                            <!-- Camera Video Feed -->
                            <video id="video" 
                                autoplay 
                                muted 
                                playsinline 
                                style="
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%) scaleX(-1);
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                    border-radius: 40px;
                                    display: block;
                                ">
                            </video>
                        </div>

                        <!-- Status Display -->
                        <div id="statusDisplay" style="
                            width: 100%;
                            text-align: center;
                            margin-bottom: 30px;
                            min-height: 60px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                        ">
                            <div id="mainStatus" style="
                                font-size: 18px;
                                font-weight: 600;
                                letter-spacing: -0.3px;
                                margin-bottom: 8px;
                            ">Position your face inside the frame</div>
                            <div id="subStatus" style="
                                font-size: 13px;
                                font-weight: 400;
                            ">Waiting to start...</div>
                        </div>

                        <!-- Progress Indicator -->
                        <div id="progressContainer" style="
                            width: 100%;
                            max-width: 200px;
                            margin-bottom: 30px;
                            display: none;
                        ">
                            <div style="
                                display: flex;
                                gap: 8px;
                                justify-content: center;
                                margin-bottom: 12px;
                            ">
                                <div id="dot1" style="
                                    width: 8px;
                                    height: 8px;
                                    border-radius: 50%;
                                    background: rgba(6, 182, 212, 0.4);
                                    animation: pulse-dot 1.5s ease-in-out infinite;
                                "></div>
                                <div id="dot2" style="
                                    width: 8px;
                                    height: 8px;
                                    border-radius: 50%;
                                    background: rgba(6, 182, 212, 0.4);
                                    animation: pulse-dot 1.5s ease-in-out infinite 0.3s;
                                "></div>
                                <div id="dot3" style="
                                    width: 8px;
                                    height: 8px;
                                    border-radius: 50%;
                                    background: rgba(6, 182, 212, 0.4);
                                    animation: pulse-dot 1.5s ease-in-out infinite 0.6s;
                                "></div>
                            </div>
                            <div style="
                                background: rgba(6, 182, 212, 0.1);
                                height: 4px;
                                border-radius: 2px;
                                overflow: hidden;
                                border: 1px solid rgba(6, 182, 212, 0.3);
                            ">
                                <div id="progressBar" style="
                                    height: 100%;
                                    width: 0%;
                                    background: linear-gradient(90deg, #06b6d4 0%, #22c55e 100%);
                                    border-radius: 2px;
                                    transition: width 0.3s ease;
                                "></div>
                            </div>
                        </div>

                        <!-- Success State (Hidden) -->
                        <div id="successContainer" style="
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            text-align: center;
                            opacity: 0;
                            pointer-events: none;
                            z-index: 10;
                        ">
                            <div style="
                                width: 100px;
                                height: 100px;
                                margin: 0 auto 20px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                background: rgba(34, 197, 94, 0.1);
                                border-radius: 50%;
                                border: 2px solid #22c55e;
                                animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
                            ">
                                <svg width="50" height="50" viewBox="0 0 50 50" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: drawCheck 0.5s ease-out 0.2s forwards; opacity: 0;">
                                    <polyline points="15 25 22 32 35 18"></polyline>
                                </svg>
                            </div>
                            <div style="color: #22c55e; font-size: 20px; font-weight: 700; margin-bottom: 8px;">Face Verified Successfully</div>
                            <div style="color: rgba(255, 255, 255, 0.6); font-size: 13px;">Redirecting you now...</div>
                        </div>

                        <!-- Action Buttons -->
                        <div style="width: 100%; display: flex; gap: 12px;">
                            <button type="button" id="startCameraBtn" onclick="startCamera()" style="
                                flex: 1;
                                padding: 14px 24px;
                                background: linear-gradient(135deg, #06b6d4 0%, #22c55e 100%);
                                color: #fff;
                                border: none;
                                border-radius: 12px;
                                font-size: 15px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                box-shadow: 0 8px 24px rgba(6, 182, 212, 0.3);
                                letter-spacing: -0.3px;
                            " onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 32px rgba(6, 182, 212, 0.4)';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(6, 182, 212, 0.3)';">
                                Start Verification
                            </button>
                            <button type="button" id="cancelBtn" onclick="closeFaceVerification()" style="
                                padding: 14px 24px;
                                background: rgba(255, 255, 255, 0.1);
                                color: rgba(255, 255, 255, 0.7);
                                border: 1px solid rgba(255, 255, 255, 0.2);
                                border-radius: 12px;
                                font-size: 15px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                letter-spacing: -0.3px;
                            " onmouseover="this.style.background='rgba(255, 255, 255, 0.15)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)';">
                                Close
                            </button>
                        </div>

                        <!-- Trust Badges -->
                        <div class="trust-badges" style="
                            width: 100%;
                            display: flex;
                            justify-content: center;
                            gap: 20px;
                            margin-top: 30px;
                            padding-top: 30px;
                            border-top: 1px solid rgba(255, 255, 255, 0.1);
                        ">
                            <div style="
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                font-size: 12px;
                                color: rgba(255, 255, 255, 0.6);
                            ">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                <span>Secure & Encrypted</span>
                            </div>
                            <div style="
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                font-size: 12px;
                                color: rgba(255, 255, 255, 0.6);
                            ">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 4 20.59 2.59 9 14.17 6.41 11.59 5 13 9 17 24 2"></polyline></svg>
                                <span>AI Verified</span>
                            </div>
                        </div>
                    </div>

                    <canvas id="canvas" style="display:none;"></canvas>
                </div>

                <script>
                    // Global variables for fast face verification
                    let modelsLoaded = false;
                    let detectionInterval = null;
                    let storedDescriptor = null;
                    let storedFacePayload = null;
                    let video = null;
                    let canvas = null;
                    let blinkCount = 0;
                    let eyeClosedFrames = 0;
                    
                    function showFaceVerificationUI() {
                        const container = document.getElementById('faceVerificationContainer');
                        if (container) container.classList.add('is-active');
                    }
                    
                    function closeFaceVerification() {
                        if (detectionInterval) {
                            clearInterval(detectionInterval);
                            detectionInterval = null;
                        }
                        const videoEl = document.getElementById('video');
                        if (videoEl && videoEl.srcObject) {
                            videoEl.srcObject.getTracks().forEach(track => track.stop());
                        }
                        
                        // Clear temporary session and redirect to login
                        fetch('<?= base_url('auth/logout') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(() => {
                            window.location.href = '<?= base_url('login?action=student') ?>';
                        }).catch(() => {
                            window.location.href = '<?= base_url('login?action=student') ?>';
                        });
                    }
                    
                    // Load face-api models
                    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.10/model/';
                    console.log('Loading face-api models...');
                    Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                    ]).then(() => {
                        modelsLoaded = true;
                        storedDescriptor = null;
                        try {
                            const descriptor = <?php echo json_encode(session()->get('temp_face_descriptor')); ?>;
                            if (descriptor) {
                                const parsed = typeof descriptor === 'string' ? JSON.parse(descriptor) : descriptor;
                                storedFacePayload = parsed;
                                if (parsed && Array.isArray(parsed.descriptor)) {
                                    storedDescriptor = new Float32Array(parsed.descriptor);
                                } else if (Array.isArray(parsed)) {
                                    storedDescriptor = new Float32Array(parsed);
                                }
                            }
                        } catch (e) {
                            console.warn('Could not load stored descriptor:', e.message);
                        }
                        console.log('✓ Face-api models and descriptor loaded');
                    }).catch(err => {
                        console.error('✗ Failed to load models:', err);
                        modelsLoaded = false;
                    });
                    
                    // Forward declare startCamera
                    async function startCamera() {
                        video = document.getElementById('video');
                        canvas = document.getElementById('canvas');
                        const startBtn = document.getElementById('startCameraBtn');
                        const mainStatus = document.getElementById('mainStatus');
                        const subStatus = document.getElementById('subStatus');
                        const progressContainer = document.getElementById('progressContainer');
                        
                        if (!modelsLoaded) {
                            alert('Face detection models are still loading. Please wait and try again.');
                            return;
                        }
                        
                        startBtn.disabled = true;
                        startBtn.textContent = 'Starting Camera...';
                        mainStatus.textContent = 'Initializing camera...';
                        
                        try {
                            const stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                            });
                            
                            video.srcObject = stream;
                            
                            startBtn.style.display = 'none';
                            progressContainer.style.display = 'block';
                            
                            mainStatus.textContent = '🔐 STRICT VERIFICATION MODE';
                            subStatus.textContent = 'Position face in animated frame (NO PHOTOS)...';

                            console.log('✓ Camera started - STRICT VERIFICATION WITH SPOOF DETECTION');
                            
                            setTimeout(() => {
                                verifyFaceWithBlinkDetection();
                            }, 300);
                            
                        } catch (err) {
                            console.error('✗ Camera error:', err);
                            alert('Camera access is required for face verification. Please allow camera access and try again.');
                            startBtn.disabled = false;
                            startBtn.textContent = 'Start Verification';
                            mainStatus.textContent = 'Camera access denied';
                            subStatus.textContent = 'Please check your camera permissions';
                        }
                    }
                    
                    // Global challenge variables
                    let currentChallenge = null;
                    let challengeStartTime = 0;
                    let challengeTimeoutId = null;
                    let faceMotionHistory = [];  // Track face movements
                    let previousLandmarks = null; // Track motion between frames
                    let consecutiveChallengeFailures = 0;  // Track failed challenges
                    
                    // Get random challenge
                    function getRandomChallenge() {
                        const challenges = ['blink', 'smile'];
                        return challenges[Math.floor(Math.random() * challenges.length)];
                    }
                    
                    // Get challenge description
                    function getChallengeText(challenge) {
                        const texts = {
                            'blink': '👁️ Blink once',
                            'smile': '😊 Smile naturally'
                        };
                        return texts[challenge] || 'Complete challenge';
                    }
                    
                    // STRICT: Calculate face motion between frames
                    function calculateFaceMotion(currentLandmarks, previousLandmarks) {
                        if (!previousLandmarks) return 0;
                        
                        let totalMotion = 0;
                        for (let i = 0; i < Math.min(currentLandmarks.length, previousLandmarks.length); i++) {
                            const dx = currentLandmarks[i].x - previousLandmarks[i].x;
                            const dy = currentLandmarks[i].y - previousLandmarks[i].y;
                            totalMotion += Math.sqrt(dx * dx + dy * dy);
                        }
                        return totalMotion / currentLandmarks.length;
                    }
                    
                    // STRICT: Detect if face is frozen (spoof attempt)
                    function detectFrozenFace(landmarks) {
                        if (!previousLandmarks) {
                            previousLandmarks = landmarks;
                            return false;
                        }
                        
                        const motion = calculateFaceMotion(landmarks, previousLandmarks);
                        faceMotionHistory.push(motion);
                        
                        // Keep only last 30 frames (500ms at 60fps)
                        if (faceMotionHistory.length > 30) {
                            faceMotionHistory.shift();
                        }
                        
                        // Check if motion is consistently zero (frozen frame/photo)
                        const avgMotion = faceMotionHistory.reduce((a, b) => a + b, 0) / faceMotionHistory.length;
                        const motionVariance = faceMotionHistory.reduce((a, m) => a + Math.pow(m - avgMotion, 2), 0) / faceMotionHistory.length;
                        
                        previousLandmarks = landmarks;
                        
                        // If average motion is very low AND variance is very low = frozen/static photo
                        console.log('Motion: ' + avgMotion.toFixed(3) + ', Variance: ' + motionVariance.toFixed(3));
                        return avgMotion < 0.5 && motionVariance < 0.1;
                    }
                    
                    // STRICT: Enhanced spoof detection
                    function detectSpoof(detections) {
                        let spoofScore = 0;
                        
                        // Check if landmarks are present and valid
                        if (!detections.landmarks || detections.landmarks.positions.length < 68) {
                            spoofScore += 0.5;
                            console.warn('❌ Invalid landmarks detected');
                        }
                        
                        // Check face size (must be sufficiently large - prevent ID photo)
                        const box = detections.detection.box;
                        if (box.width < 120 || box.height < 120) {
                            spoofScore += 0.6;  // Increased penalty
                            console.warn('❌ Face too small - possible ID photo or screenshot');
                        }
                        
                        // Check detection score (low score = less confident = fake)
                        if (detections.detection.score < 0.75) {
                            spoofScore += 0.4;  // Increased threshold and penalty
                            console.warn('❌ Low detection confidence:', detections.detection.score);
                        }
                        
                        // Check for frozen face (no motion = photo/screenshot)
                        if (detections.landmarks) {
                            if (detectFrozenFace(detections.landmarks.positions)) {
                                spoofScore += 0.5;
                                console.warn('❌ Frozen face detected - static photo!');
                            }
                        }
                        
                        // Check eye symmetry (prints/photos often have artifacts)
                        if (detections.landmarks) {
                            const landmarks = detections.landmarks.positions;
                            const leftEye = landmarks.slice(36, 42);
                            const rightEye = landmarks.slice(42, 48);
                            const leftEAR = calculateEyeAspectRatio(leftEye);
                            const rightEAR = calculateEyeAspectRatio(rightEye);
                            
                            // Both eyes should be similar (not perfectly identical)
                            const earDiff = Math.abs(leftEAR - rightEAR);
                            if (earDiff > 0.15) {  // Stricter symmetry requirement
                                spoofScore += 0.25;  // Increased penalty
                                console.warn('❌ Eye asymmetry detected - possible fake');
                            }
                        }
                        
                        console.log('Spoof Score: ' + spoofScore.toFixed(2));
                        return spoofScore;
                    }
                    
                    // Advanced face verification with random challenges
                    async function verifyFaceWithBlinkDetection() {
                        if (detectionInterval) {
                            clearInterval(detectionInterval);
                            detectionInterval = null;
                        }

                        const mainStatus = document.getElementById('mainStatus');
                        const subStatus = document.getElementById('subStatus');
                        const progressBar = document.getElementById('progressBar');
                        const glowRing = document.getElementById('glowRing');
                        
                        mainStatus.textContent = 'Detecting your face...';
                        subStatus.textContent = 'Position face in frame...';
                        
                        const maxFaceDetectionTime = 5000;
                        let detectionTimeout = 0;
                        let faceDetectedInFirstFrame = false;
                        let verificationState = 'detecting_face';
                        faceMotionHistory = [];
                        previousLandmarks = null;

                        console.log('Starting STRICT face verification...');
                        
                        return new Promise(async (resolve) => {
                            detectionInterval = setInterval(async () => {
                                detectionTimeout += 15;
                                
                                try {
                                    if (!modelsLoaded || !video) return;

                                    const progress = Math.min(100, (detectionTimeout / maxFaceDetectionTime) * 100);
                                    progressBar.style.width = progress + '%';

                                    const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                                        inputSize: 320,
                                        scoreThreshold: 0.5
                                    })).withFaceLandmarks().withFaceDescriptor();

                                    if (detections && detections.landmarks && detections.detection) {
                                        // STRICT: Spoof detection
                                        const spoofScore = detectSpoof(detections);
                                        if (spoofScore > 0.35) {  // Much stricter threshold
                                            console.warn('❌ STRICT SPOOF DETECTION TRIGGERED! Score:', spoofScore);
                                            clearInterval(detectionInterval);
                                            mainStatus.textContent = '⛔ Spoof attempt blocked';
                                            subStatus.textContent = 'Only real faces allowed - Photos/Screens detected';
                                            glowRing.style.borderColor = '#ef4444';
                                            glowRing.style.boxShadow = '0 0 80px rgba(239, 68, 68, 0.9)';
                                            
                                            setTimeout(() => {
                                                alert('❌ SECURITY ALERT!\n\nSpoof attempt detected:\n- Static photo or frozen screenshot\n- Artificial face characteristics\n\nOnly real, moving human faces are allowed.\n\nPlease try again with a real face.');
                                                retryFaceVerification();
                                            }, 1000);
                                            resolve();
                                            return;
                                        }

                                        if (!faceDetectedInFirstFrame) {
                                            faceDetectedInFirstFrame = true;
                                            console.log('✓ Real human face detected (passed spoof checks)');
                                            clearInterval(detectionInterval);
                                            
                                            // Start challenge verification
                                            setTimeout(() => {
                                                startChallengeVerification(detections.descriptor);
                                            }, 500);
                                            resolve();
                                            return;
                                        }
                                    } else {
                                        if (faceDetectedInFirstFrame && detectionTimeout > 500) {
                                            mainStatus.textContent = '⚠️ Face out of frame';
                                            subStatus.textContent = 'Position your face inside the frame';
                                        }
                                    }

                                    if (detectionTimeout >= maxFaceDetectionTime) {
                                        clearInterval(detectionInterval);
                                        mainStatus.textContent = 'No face detected';
                                        subStatus.textContent = 'Retrying...';
                                        console.warn('Face detection timeout - auto retrying');
                                        
                                        setTimeout(() => {
                                            retryFaceVerification();
                                        }, 500);
                                        resolve();
                                    }

                                } catch (err) {
                                    console.error('Detection error:', err);
                                }
                            }, 15);
                        });
                    }
                    
                    // STRICT: Start challenge-based verification with motion requirement
                    async function startChallengeVerification(faceDescriptor) {
                        const mainStatus = document.getElementById('mainStatus');
                        const subStatus = document.getElementById('subStatus');
                        const progressBar = document.getElementById('progressBar');
                        const glowRing = document.getElementById('glowRing');
                        
                        currentChallenge = getRandomChallenge();
                        const challengeText = getChallengeText(currentChallenge);
                        
                        mainStatus.textContent = '✓ Face verified';
                        subStatus.textContent = 'Complete challenge: ' + challengeText;
                        progressBar.style.width = '0%';
                        
                        console.log('🔐 Challenge: ' + currentChallenge);
                        
                        challengeStartTime = Date.now();
                        const challengeTimeout = 6000; // 6 seconds to complete challenge
                        let challengeCompleted = false;
                        let challengeMotionDetected = false;
                        let motionRequiredCount = 0;
                        faceMotionHistory = [];
                        previousLandmarks = null;
                        
                        return new Promise((resolve) => {
                            detectionInterval = setInterval(async () => {
                                const elapsed = Date.now() - challengeStartTime;
                                const progress = Math.min(100, (elapsed / challengeTimeout) * 100);
                                progressBar.style.width = progress + '%';
                                
                                try {
                                    if (!modelsLoaded || !video) return;
                                    
                                    const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                                        inputSize: 320,
                                        scoreThreshold: 0.5
                                    })).withFaceLandmarks();
                                    
                                    if (detections && detections.landmarks && !challengeCompleted) {
                                        // Check for motion (not frozen)
                                        if (detections.landmarks.positions && previousLandmarks) {
                                            const motion = calculateFaceMotion(detections.landmarks.positions, previousLandmarks);
                                            if (motion > 1.2) {  // Strict motion requirement
                                                challengeMotionDetected = true;
                                                motionRequiredCount++;
                                            }
                                        }
                                        previousLandmarks = detections.landmarks.positions;
                                        
                                        let completed = false;
                                        
                                        if (currentChallenge === 'blink') {
                                            completed = detectBlinkTwice(detections.landmarks.positions);
                                        } else if (currentChallenge === 'smile') {
                                            completed = detectSmile(detections.landmarks.positions);
                                        }
                                        
                                        // Require sufficient motion during challenge
                                        if (completed && motionRequiredCount > 3) {  // Strict motion requirement
                                            challengeCompleted = true;
                                            consecutiveChallengeFailures = 0;  // Reset failure counter on success
                                            console.log('✓ Challenge completed: ' + currentChallenge);
                                            clearInterval(detectionInterval);
                                            mainStatus.textContent = '✓ Challenge verified';
                                            subStatus.textContent = 'Matching identity...';
                                            progressBar.style.width = '100%';
                                            
                                            // Get fresh descriptor for strict matching
                                            const freshDetections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                                                inputSize: 320,
                                                scoreThreshold: 0.5
                                            })).withFaceLandmarks().withFaceDescriptor();
                                            
                                            setTimeout(() => {
                                                if (freshDetections && freshDetections.descriptor) {
                                                    performStrictFaceMatch(freshDetections.descriptor);
                                                } else {
                                                    performStrictFaceMatch(faceDescriptor);
                                                }
                                                resolve();
                                            }, 800);
                                            return;
                                        } else if (completed && motionRequiredCount <= 5) {
                                            console.warn('⚠️ Challenge detected but insufficient motion - possible spoof');
                                        }
                                    }
                                    
                                    if (elapsed >= challengeTimeout) {
                                        clearInterval(detectionInterval);
                                        if (!challengeCompleted) {
                                            consecutiveChallengeFailures++;  // Increment failure counter
                                            console.warn('⚠️ Challenge failed - attempt ' + consecutiveChallengeFailures + ' of 2');
                                            
                                            if (consecutiveChallengeFailures >= 2) {
                                                // After 2 failures, require re-login
                                                mainStatus.textContent = '❌ 2 challenges failed';
                                                subStatus.textContent = 'Redirecting to login...';
                                                console.warn('❌ 2 consecutive challenges failed - returning to login');
                                                
                                                setTimeout(() => {
                                                    alert('❌ VERIFICATION FAILED!\n\n2 unsuccessful challenge attempts.\n\nPlease log in again with your username and password.');
                                                    closeFaceVerification();
                                                }, 1500);
                                            } else {
                                                mainStatus.textContent = '⏱️ Challenge timeout (' + consecutiveChallengeFailures + '/2)';
                                                subStatus.textContent = 'Retrying...';
                                                console.warn('Challenge timeout - insufficient motion or challenge not completed');
                                                
                                                setTimeout(() => {
                                                    retryFaceVerification();
                                                }, 500);
                                            }
                                        }
                                        resolve();
                                    }
                                    
                                } catch (err) {
                                    console.error('Challenge error:', err);
                                }
                            }, 15);
                        });
                    }
                    
                    // Blink detection - requires single eye closure
                    function detectBlinkTwice(landmarks) {
                        const leftEye = landmarks.slice(36, 42);
                        const rightEye = landmarks.slice(42, 48);
                        const leftEAR = calculateEyeAspectRatio(leftEye);
                        const rightEAR = calculateEyeAspectRatio(rightEye);
                        const avgEAR = (leftEAR + rightEAR) / 2;
                        
                        // Smooth threshold for blink detection
                        const blink_threshold = 0.25;  // Relaxed for smooth detection
                        
                        if (avgEAR < blink_threshold) {
                            window.blinkDetectionCounter = (window.blinkDetectionCounter || 0) + 1;
                            // Quick blink detection (2 frames)
                            if (window.blinkDetectionCounter >= 2) {
                                window.blinkDetectionCounter = 0;
                                console.log('✓ Blink detected');
                                return true;
                            }
                        } else if (avgEAR > 0.30) {  // Eyes clearly open
                            if (window.blinkDetectionCounter > 0 && window.blinkDetectionCounter < 2) {
                                // Reset if blink was too short
                                window.blinkDetectionCounter = 0;
                            }
                        }
                        return false;
                    }
                    
                    // Head turn left/right detection - relaxed thresholds
                    function detectHeadTurn(landmarks, direction) {
                        const nose = landmarks[30];
                        const leftEar = landmarks[0];
                        const rightEar = landmarks[16];
                        const chin = landmarks[8];
                        const forehead = landmarks[27];
                        
                        const noseToLeftEar = Math.abs(nose.x - leftEar.x);
                        const noseToRightEar = Math.abs(nose.x - rightEar.x);
                        
                        // Relaxed threshold for easier head turns
                        if (direction === 'left') {
                            const turned = noseToLeftEar > noseToRightEar + 15;  // Reduced from 40 to 15
                            if (turned) console.log('✓ Head turn left detected');
                            return turned;
                        } else {
                            const turned = noseToRightEar > noseToLeftEar + 15;  // Reduced from 40 to 15
                            if (turned) console.log('✓ Head turn right detected');
                            return turned;
                        }
                    }
                    
                    // Smile detection - relaxed
                    function detectSmile(landmarks) {
                        const mouthLeft = landmarks[48];
                        const mouthRight = landmarks[54];
                        const mouthTop = landmarks[51];
                        const mouthBottom = landmarks[57];
                        
                        const mouthWidth = Math.abs(mouthRight.x - mouthLeft.x);
                        const mouthHeight = Math.abs(mouthBottom.y - mouthTop.y);
                        
                        // Strict threshold for smile detection
                        const smileRatio = mouthWidth / (mouthHeight + 0.1);
                        const smiled = smileRatio > 3.5;  // Strict smile requirement
                        
                        if (smiled) console.log('✓ Smile detected - ratio:', smileRatio.toFixed(2));
                        return smiled;
                    }
                    
                    // Head tilt up detection - relaxed
                    function detectHeadTilt(landmarks, direction) {
                        const nose = landmarks[30];
                        const chin = landmarks[8];
                        const forehead = landmarks[27];
                        const nostrilLeft = landmarks[31];
                        const nostrilRight = landmarks[35];
                        
                        if (direction === 'up') {
                            // Relaxed upward tilt detection
                            const noseHeight = Math.abs(chin.y - forehead.y);
                            const chinDistance = Math.abs(chin.y - nose.y);
                            
                            const tilted = chinDistance > noseHeight * 0.25 && nose.y < forehead.y - 5;  // Relaxed thresholds
                            if (tilted) console.log('✓ Head tilt up detected');
                            return tilted;
                        }
                        return false;
                    }
                    
                    function calculateEyeAspectRatio(eye) {
                        const A = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
                        const B = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
                        const C = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
                        return (A + B) / (2.0 * C);
                    }
                    
                    async function verifyFaceWithFaceApi(currentDescriptor) {
                        if (!storedDescriptor) {
                            throw new Error('No registered face descriptor found');
                        }

                        const distance = faceapi.euclideanDistance(currentDescriptor, storedDescriptor);
                        const strictThreshold = 0.35;
                        return { success: distance < strictThreshold, distance };
                    }

                    async function performStrictFaceMatch(currentDescriptor) {
                        const mainStatus = document.getElementById('mainStatus');
                        const subStatus = document.getElementById('subStatus');
                        const successContainer = document.getElementById('successContainer');
                        const glowRing = document.getElementById('glowRing');

                        try {
                            const matchResult = await verifyFaceWithFaceApi(currentDescriptor);
                            console.log('Face match result:', matchResult);

                                if (matchResult.success) {
                                glowRing.style.animation = 'none';
                                glowRing.style.borderColor = '#22c55e';
                                glowRing.style.boxShadow = '0 0 50px rgba(34, 197, 94, 0.8), inset 0 0 40px rgba(34, 197, 94, 0.2)';
                                
                                successContainer.style.opacity = '1';
                                successContainer.style.pointerEvents = 'auto';
                                mainStatus.style.display = 'none';
                                subStatus.style.display = 'none';
                                
                                fetch('<?= base_url('login/face-verify') ?>', {method: 'POST'})
                                    .then(r => r.json())
                                    .then(d => { 
                                        if(d.success) {
                                            window.location.href = '<?= base_url('voting') ?>';
                                        } else {
                                            alert('Verification failed: ' + (d.message || 'Unknown error'));
                                            retryFaceVerification();
                                        }
                                    })
                                    .catch(err => {
                                        console.error('Verification failed:', err);
                                        alert('Verification error: ' + err.message);
                                        retryFaceVerification();
                                    });
                            } else {
                                console.warn('❌ FACE MISMATCH! ', matchResult);
                                mainStatus.textContent = '❌ Face does not match';
                                subStatus.textContent = 'Retrying...';
                                glowRing.style.borderColor = '#ef4444';
                                glowRing.style.boxShadow = '0 0 80px rgba(239, 68, 68, 0.9)';
                                setTimeout(() => {
                                    alert('Face not recognized. Please try again or log in using your Student ID and password.');
                                    retryFaceVerification();
                                }, 800);
                            }
                        } catch (err) {
                            console.error('Face matching error:', err);
                            mainStatus.textContent = 'Verification Error';
                            subStatus.textContent = 'Retrying...';
                            
                            setTimeout(() => {
                                retryFaceVerification();
                            }, 1500);
                        }
                    }
                    
                    // Keep original function for compatibility
                    async function performFaceMatch(currentDescriptor) {
                        return performStrictFaceMatch(currentDescriptor);
                    }
                    
                    function retryFaceVerification() {
                        const mainStatus = document.getElementById('mainStatus');
                        const subStatus = document.getElementById('subStatus');
                        const progressContainer = document.getElementById('progressContainer');
                        const progressBar = document.getElementById('progressBar');
                        const glowRing = document.getElementById('glowRing');
                        const successContainer = document.getElementById('successContainer');
                        
                        // Reset UI
                        successContainer.style.opacity = '0';
                        successContainer.style.pointerEvents = 'none';
                        mainStatus.style.display = 'block';
                        subStatus.style.display = 'block';
                        mainStatus.textContent = '🔐 STRICT VERIFICATION MODE';
                        subStatus.textContent = 'Position face in frame (no photos/screens)...';
                        progressContainer.style.display = 'block';
                        progressBar.style.width = '0%';
                        glowRing.style.animation = 'pulseGlow 2s infinite';
                        glowRing.style.borderColor = '#06b6d4';
                        glowRing.style.boxShadow = '0 0 30px rgba(6, 182, 212, 0.4), inset 0 0 30px rgba(6, 182, 212, 0.1)';
                        
                        // Clear challenge state
                        currentChallenge = null;
                        window.blinkDetectionCounter = 0;
                        faceMotionHistory = [];
                        previousLandmarks = null;
                        consecutiveChallengeFailures = 0;  // Reset failure counter on retry
                        
                        // Restart detection
                        verifyFaceWithBlinkDetection();
                    }

                    window.addEventListener('load', function() {
                        showFaceVerificationUI();
                        setTimeout(function() {
                            const btn = document.getElementById('startCameraBtn');
                            if (btn && modelsLoaded) btn.click();
                            else if (btn) {
                                const waitModels = setInterval(function() {
                                    if (modelsLoaded) {
                                        clearInterval(waitModels);
                                        btn.click();
                                    }
                                }, 200);
                            }
                        }, 400);
                    });
                </script>
            <?php else: ?>
                <script>
                // Geolocation logic only for student login when the related elements exist
                (function(){
                    const latitudeInput = document.getElementById('latitude');
                    const longitudeInput = document.getElementById('longitude');
                    const locationStatus = document.getElementById('locationStatus');
                    if (!latitudeInput || !longitudeInput || !locationStatus) return;

                    let geolocationComplete = false;
                    let geolocationAttempted = false;

                    function updateLocationMessage(message, isError = false) {
                        locationStatus.textContent = message;
                        locationStatus.className = 'mt-2 small text-' + (isError ? 'danger' : 'muted');
                    }

                    // Get location on page load
                    if (navigator.geolocation) {
                        geolocationAttempted = true;
                        navigator.geolocation.getCurrentPosition(
                            function (position) {
                                latitudeInput.value = position.coords.latitude;
                                longitudeInput.value = position.coords.longitude;
                                geolocationComplete = true;
                                updateLocationMessage('Campus location detected.');
                                console.log('✓ Location acquired: ' + position.coords.latitude + ', ' + position.coords.longitude);
                            },
                            function (error) {
                                geolocationComplete = true;
                                // Still mark as complete even on error - allow submission without location
                                console.warn('Geolocation error:', error);
                                if (error.code === error.PERMISSION_DENIED) {
                                    updateLocationMessage('Location permission denied. You can still login, but campus verification may be limited.', true);
                                } else if (error.code === error.TIMEOUT) {
                                    updateLocationMessage('Location request timed out. Proceeding without location verification.', true);
                                } else {
                                    updateLocationMessage('Could not determine location. Proceeding without location verification.', true);
                                }
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 8000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        geolocationComplete = true;
                        updateLocationMessage('Geolocation is not supported by this browser.', true);
                    }

                    // Handle student form submission specifically
                    const studentForm = document.getElementById('studentLoginForm');
                    if (studentForm) {
                        studentForm.addEventListener('submit', function(e) {
                            // If geolocation was attempted but not yet complete, wait a bit
                            if (geolocationAttempted && !geolocationComplete) {
                                e.preventDefault();
                                updateLocationMessage('Acquiring location... Please wait.', true);

                                // Wait up to 2 more seconds for geolocation
                                let waitTime = 0;
                                const waitInterval = setInterval(function() {
                                    waitTime += 100;
                                    if (geolocationComplete || waitTime > 2000) {
                                        clearInterval(waitInterval);
                                        studentForm.submit(); // Submit after waiting
                                    }
                                }, 100);
                            }
                            // Otherwise allow form to submit normally
                        });
                    }
                })();
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
