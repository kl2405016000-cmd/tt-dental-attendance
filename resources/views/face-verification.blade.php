<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Face Scanner — TT Dental Lab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: #080a10;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
        }
        .kiosk-topbar {
            width: 100%;
            background: rgba(15,17,30,0.98);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .kiosk-brand { display: flex; align-items: center; gap: 12px; }
        .kiosk-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .kiosk-brand-text h1 { font-size: 15px; font-weight: 800; color: #ffffff; }
        .kiosk-brand-text span { font-size: 11px; color: #64748b; font-weight: 500; }
        .kiosk-clock { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #94a3b8; }
        .kiosk-clock-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .kiosk-layout {
            width: 100%; max-width: 1100px;
            padding: 28px 24px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            align-items: start;
        }
        .camera-panel {
            background: #0d0f1a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
        }
        .camera-panel-header {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: space-between;
        }
        .camera-panel-title { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; }
        .cam-pill {
            display: flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 999px;
            font-size: 11.5px; font-weight: 700;
            background: rgba(239,68,68,0.12); color: #f87171;
            border: 1px solid rgba(239,68,68,0.2);
        }
        .cam-pill.active { background: rgba(74,222,128,0.12); color: #4ade80; border-color: rgba(74,222,128,0.2); }
        .cam-pill-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .cam-pill.active .cam-pill-dot { animation: pulse 1.5s infinite; }
        .camera-viewport { position: relative; width: 100%; background: #000; aspect-ratio: 4/3; overflow: hidden; }
        #camera { display: block; width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
        #overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: scaleX(-1); pointer-events: none; }
        .scan-guide {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 230px; height: 290px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%; pointer-events: none;
        }
        .scan-guide::before {
            content: ''; position: absolute; inset: -7px;
            border: 2px dashed rgba(79,70,229,0.45);
            border-radius: 50%;
            animation: spin 10s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .scan-corner { position: absolute; width: 22px; height: 22px; border-color: #4f46e5; border-style: solid; border-width: 0; }
        .scan-corner.tl { top: 16px; left: 16px; border-top-width: 3px; border-left-width: 3px; border-radius: 4px 0 0 0; }
        .scan-corner.tr { top: 16px; right: 16px; border-top-width: 3px; border-right-width: 3px; border-radius: 0 4px 0 0; }
        .scan-corner.bl { bottom: 16px; left: 16px; border-bottom-width: 3px; border-left-width: 3px; border-radius: 0 0 0 4px; }
        .scan-corner.br { bottom: 16px; right: 16px; border-bottom-width: 3px; border-right-width: 3px; border-radius: 0 0 4px 0; }
        .scan-line {
            position: absolute; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, rgba(79,70,229,0.8), transparent);
            animation: scanLine 3s ease-in-out infinite; display: none;
        }
        .scan-line.active { display: block; }
        @keyframes scanLine { 0% { top: 8%; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 92%; opacity: 0; } }
        .camera-idle {
            position: absolute; inset: 0;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;
            background: #050608;
        }
        .camera-idle svg { opacity: 0.15; }
        .camera-idle-text { font-size: 13px; color: rgba(255,255,255,0.25); font-weight: 600; }
        .info-panel { display: flex; flex-direction: column; gap: 14px; }
        .icard {
            background: #0d0f1a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px; padding: 16px;
        }
        .icard-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; margin-bottom: 10px; }
        #statusBox {
            padding: 13px 15px; border-radius: 10px;
            font-size: 13px; font-weight: 700; text-align: center;
            background: rgba(245,158,11,0.08); color: #fbbf24;
            border: 1px solid rgba(245,158,11,0.2); line-height: 1.4;
        }
        #statusBox.success { background: rgba(5,150,105,0.08); color: #34d399; border-color: rgba(5,150,105,0.2); }
        #statusBox.error { background: rgba(220,38,38,0.08); color: #f87171; border-color: rgba(220,38,38,0.2); }
        #faceCountBox { margin-top: 7px; font-size: 12px; color: #475569; text-align: center; }
        .emp-card {
            background: #0d0f1a;
            border: 1px solid rgba(5,150,105,0.3);
            border-radius: 16px; padding: 18px;
            text-align: center; display: none;
        }
        .emp-card.visible { display: block; }
        .emp-avatar {
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, #059669, #047857);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 900; color: white;
            margin: 0 auto 12px;
            border: 3px solid rgba(5,150,105,0.35);
        }
        .emp-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700;
            background: rgba(5,150,105,0.12); color: #34d399;
            border: 1px solid rgba(5,150,105,0.2); margin-bottom: 10px;
        }
        .emp-name { font-size: 19px; font-weight: 900; color: #ffffff; margin-bottom: 3px; }
        .emp-id { font-size: 12px; color: #64748b; font-weight: 600; }
        .emp-dist { font-size: 11px; color: #475569; margin-top: 5px; }
        .att-actions { display: none; gap: 8px; margin-top: 12px; }
        .att-actions.visible { display: flex; }
        .btn-att {
            flex: 1; border: none; padding: 11px 10px; border-radius: 9px;
            font-size: 12.5px; font-weight: 800; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 5px;
            transition: all 0.2s; font-family: inherit;
        }
        .btn-ci { background: rgba(5,150,105,0.12); color: #34d399; border: 1.5px solid rgba(5,150,105,0.25); }
        .btn-ci:hover:not(:disabled) { background: rgba(5,150,105,0.22); border-color: rgba(5,150,105,0.45); }
        .btn-co { background: rgba(220,38,38,0.1); color: #f87171; border: 1.5px solid rgba(220,38,38,0.22); }
        .btn-co:hover:not(:disabled) { background: rgba(220,38,38,0.2); border-color: rgba(220,38,38,0.4); }
        .btn-att:disabled { opacity: 0.35; cursor: not-allowed; }
        .action-btns { display: flex; flex-direction: column; gap: 9px; }
        .btn-kiosk {
            width: 100%; border: none; padding: 13px 16px; border-radius: 11px;
            font-size: 14px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s; font-family: inherit;
        }
        .btn-start { background: linear-gradient(135deg, #1e293b, #334155); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.1); }
        .btn-start:hover:not(:disabled) { background: linear-gradient(135deg, #334155, #475569); }
        .btn-verify { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; box-shadow: 0 4px 14px rgba(79,70,229,0.3); }
        .btn-verify:hover:not(:disabled) { background: linear-gradient(135deg, #4338ca, #6d28d9); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(79,70,229,0.45); }
        .btn-kiosk:disabled { opacity: 0.35; cursor: not-allowed; transform: none !important; box-shadow: none !important; }
        .btn-back {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 10px 16px; border-radius: 9px;
            font-size: 13px; font-weight: 600; color: #64748b;
            text-decoration: none; border: 1px solid rgba(255,255,255,0.06);
            background: transparent; transition: all 0.2s;
        }
        .btn-back:hover { color: #94a3b8; background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.1); }
        .steps-card { background: #0d0f1a; border: 1px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 14px 16px; }
        .steps-title { font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 10px; }
        .step-row { display: flex; align-items: flex-start; gap: 9px; margin-bottom: 7px; font-size: 12px; color: #64748b; }
        .step-row:last-child { margin-bottom: 0; }
        .snum { width: 19px; height: 19px; border-radius: 50%; background: rgba(79,70,229,0.14); color: #818cf8; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.5px; }
        #debugBox { display: none; margin-top: 10px; padding: 11px 13px; border-radius: 9px; background: rgba(245,158,11,0.07); border: 1px solid rgba(245,158,11,0.18); color: #fbbf24; font-size: 11.5px; white-space: pre-wrap; word-break: break-word; }
        @media (max-width: 820px) {
            .kiosk-layout { grid-template-columns: 1fr; padding: 16px 12px; gap: 16px; }
            .scan-guide { width: 160px; height: 210px; }
            .camera-viewport { aspect-ratio: 4/3; max-height: 48vh; }
            .att-actions { flex-wrap: wrap; }
            .att-actions .btn-att { min-width: 130px; }
        }
        @media (max-width: 500px) {
            .kiosk-topbar { padding: 10px 12px; }
            .kiosk-brand-text h1 { font-size: 13px; }
            .kiosk-brand-text span { display: none; }
            .kiosk-clock { font-size: 11.5px; }
            .scan-guide { width: 135px; height: 180px; }
            .emp-name { font-size: 17px; }
            .btn-kiosk { padding: 11px 14px; font-size: 13.5px; }
        }
    </style>
</head>
<body>

<div class="kiosk-topbar">
    <div class="kiosk-brand">
        <div class="kiosk-brand-icon">
            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
        </div>
        <div class="kiosk-brand-text">
            <h1>TT Dental Laboratory</h1>
            <span>Biometric Attendance Kiosk</span>
        </div>
    </div>
    <div class="kiosk-clock">
        <div class="kiosk-clock-dot"></div>
        <span id="clockDisplay">{{ now()->format('H:i:s') }}</span>
        <span style="opacity:0.3; margin:0 5px;">|</span>
        <span>{{ now()->format('D, d M Y') }}</span>
    </div>
</div>

<div class="kiosk-layout">

    <div class="camera-panel">
        <div class="camera-panel-header">
            <div class="camera-panel-title">Live Camera Feed</div>
            <div class="cam-pill" id="cameraStatusPill">
                <div class="cam-pill-dot"></div>
                <span id="cameraStatusText">Camera Off</span>
            </div>
        </div>
        <div class="camera-viewport">
            <div class="camera-idle" id="cameraIdle">
                <svg width="60" height="60" fill="none" stroke="white" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="1.2"/></svg>
                <div class="camera-idle-text">
                    <button type="button" class="btn-start" style="padding: 10px 20px; font-size: 14px; border-radius: 8px; cursor: pointer;" onclick="document.getElementById('startCamera').click();">Start Camera</button>
                </div>
            </div>
            <video id="camera" autoplay muted playsinline style="display:none;"></video>
            <canvas id="overlay"></canvas>
            <div class="scan-guide" id="scanGuide" style="display:none;"></div>
            <div id="scanCorners" style="display:none; position:absolute; inset:0; pointer-events:none;">
                <div class="scan-corner tl"></div>
                <div class="scan-corner tr"></div>
                <div class="scan-corner bl"></div>
                <div class="scan-corner br"></div>
            </div>
            <div class="scan-line" id="scanLine"></div>
        </div>
    </div>

    <div class="info-panel">

        <div class="icard">
            <div class="icard-label">System Status</div>
            <div id="statusBox">⏳ Loading face recognition models...</div>
            <div id="faceCountBox">Initialising biometric system, please wait.</div>
        </div>

        <div class="emp-card" id="empCard">
            <div class="emp-avatar" id="empAvatar">?</div>
            <div class="emp-badge">
                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Identity Verified
            </div>
            <div class="emp-name" id="empName">—</div>
            <div class="emp-id" id="empIdText">—</div>
            <div class="emp-dist" id="empDist"></div>
            <div class="att-actions" id="attActions">
                <button type="button" id="clockInButton" class="btn-att btn-ci">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                    Clock In
                </button>
                <button type="button" id="startBreakButton" class="btn-att" style="background: rgba(245,158,11,0.12); color: #fbbf24; border: 1.5px solid rgba(245,158,11,0.25); display: none;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Start Break
                </button>
                <button type="button" id="endBreakButton" class="btn-att" style="background: rgba(56,189,248,0.12); color: #38bdf8; border: 1.5px solid rgba(56,189,248,0.25); display: none;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    End Break
                </button>
                <button type="button" id="clockOutButton" class="btn-att btn-co">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                    Clock Out
                </button>
            </div>
        </div>

        <div class="action-btns">
            <button type="button" id="startCamera" class="btn-kiosk btn-start" disabled>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
                Start Camera
            </button>
            <button type="button" id="verifyFace" class="btn-kiosk btn-verify" disabled>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Verify My Face
            </button>
            <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard')) : '/' }}" class="btn-back">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </div>

        <div class="steps-card">
            <div class="steps-title">How to Use</div>
            <div class="step-row"><div class="snum">1</div><div>Click <strong style="color:#94a3b8;">Start Camera</strong> and allow camera permission when prompted.</div></div>
            <div class="step-row"><div class="snum">2</div><div>Position your face within the oval guide shown on screen.</div></div>
            <div class="step-row"><div class="snum">3</div><div>When detected, click <strong style="color:#94a3b8;">Verify My Face</strong> to authenticate.</div></div>
            <div class="step-row"><div class="snum">4</div><div>Select <strong style="color:#34d399;">Clock In</strong> or <strong style="color:#f87171;">Clock Out</strong> after verification.</div></div>
        </div>

        <div id="debugBox"></div>

    </div>
</div>

<script>
const video = document.getElementById('camera');
const canvas = document.getElementById('overlay');
const statusBox = document.getElementById('statusBox');
const faceCountBox = document.getElementById('faceCountBox');
const startButton = document.getElementById('startCamera');
const verifyButton = document.getElementById('verifyFace');
const empCard = document.getElementById('empCard');
const empAvatar = document.getElementById('empAvatar');
const empName = document.getElementById('empName');
const empIdText = document.getElementById('empIdText');
const empDist = document.getElementById('empDist');
const attActions = document.getElementById('attActions');
const clockInButton = document.getElementById('clockInButton');
const clockOutButton = document.getElementById('clockOutButton');
const startBreakButton = document.getElementById('startBreakButton');
const endBreakButton = document.getElementById('endBreakButton');
const debugBox = document.getElementById('debugBox');
const cameraIdle = document.getElementById('cameraIdle');
const scanGuide = document.getElementById('scanGuide');
const scanCorners = document.getElementById('scanCorners');
const scanLine = document.getElementById('scanLine');
const cameraStatusPill = document.getElementById('cameraStatusPill');
const cameraStatusText = document.getElementById('cameraStatusText');

let cameraStream = null, latestDescriptor = null, latestFace = null;
let detectionRunning = false, isVerifying = false;
let verifiedEmployee = null, attendanceState = null;

setInterval(() => { const el = document.getElementById('clockDisplay'); if (el) el.textContent = new Date().toLocaleTimeString('en-MY', { hour12: false }); }, 1000);

function setStatus(text, type='') { statusBox.textContent = text; statusBox.className = type; }
function setFaceCount(text) { faceCountBox.textContent = text; }
function showDebug(msg) { debugBox.style.display = 'block'; debugBox.textContent = msg; }
function setCameraStatus(active) {
    if (active) { cameraStatusPill.classList.add('active'); cameraStatusText.textContent = 'Camera Active'; }
    else { cameraStatusPill.classList.remove('active'); cameraStatusText.textContent = 'Camera Off'; }
}

async function loadAttendanceStatus() {
    if (!verifiedEmployee || !verifiedEmployee.id) return;
    try {
        const r = await fetch('/attendance/status', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest' }, body: JSON.stringify({ employee_id: verifiedEmployee.id }) });
        const d = await r.json();
        if (!r.ok || !d.success) throw new Error(d.message || 'Error checking status');
        attendanceState = d.status;
        if (d.status === 'not_clocked_in') {
            setStatus('✓ Identity Verified — Ready to Clock In', 'success');
            setFaceCount(verifiedEmployee.name + ' has not clocked in today.');
            clockInButton.style.display = 'inline-flex'; clockOutButton.style.display = 'none';
            startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none';
        } else if (d.status === 'clocked_in') {
            setStatus('✓ Already Clocked In — Break & Clock Out Available', 'success');
            setFaceCount(verifiedEmployee.name + ' is currently on duty.');
            clockInButton.style.display = 'none'; clockOutButton.style.display = 'inline-flex';
            startBreakButton.style.display = 'inline-flex'; endBreakButton.style.display = 'none';
        } else if (d.status === 'on_break') {
            setStatus('✓ Currently on Break — End Break Available', 'success');
            setFaceCount(verifiedEmployee.name + ' is currently on a break.');
            clockInButton.style.display = 'none'; clockOutButton.style.display = 'none';
            startBreakButton.style.display = 'none'; endBreakButton.style.display = 'inline-flex';
        } else if (d.status === 'completed') {
            setStatus('✓ Attendance Completed for Today', 'success');
            setFaceCount(verifiedEmployee.name + ' has completed attendance today.');
            clockInButton.style.display = 'none'; clockOutButton.style.display = 'none';
            startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none';
        }
    } catch(e) { console.error(e); setStatus('Face verified — Unable to load attendance status', 'error'); showDebug('Status error:\n' + e.message); }
}

async function loadFaceModels() {
    try {
        setStatus('⏳ Loading face recognition models...', '');
        setFaceCount('Please wait while the biometric system initialises.');
        const URL = 'https://justadudewhohacks.github.io/face-api.js/models';
        await faceapi.nets.tinyFaceDetector.loadFromUri(URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(URL);
        setStatus('✓ Biometric System Ready', 'success');
        setFaceCount('System loaded. Click "Start Camera" to begin.');
        startButton.disabled = false;
    } catch(e) {
        setStatus('✗ Unable to load face recognition models', 'error');
        setFaceCount('Check internet connection and refresh the page.');
        showDebug('Model error:\n' + e.message);
    }
}

async function startCamera() {
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setStatus('✗ Camera Not Available', 'error');
            const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
            if (!window.isSecureContext && !isLocal) {
                setFaceCount('Mobile browsers require HTTPS or localhost for camera access.');
                showDebug('Insecure origin (' + window.location.origin + '). Camera is blocked by mobile browser security. Use Chrome USB Port Forwarding or enable the insecure-origins flag.');
            } else {
                setFaceCount('Your browser does not support camera access.');
            }
            return;
        }
        setStatus('Requesting camera permission...', '');
        cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
        video.srcObject = cameraStream;
        await video.play();
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        cameraIdle.style.display = 'none'; video.style.display = 'block';
        scanGuide.style.display = 'block'; scanCorners.style.display = 'block';
        scanLine.classList.add('active');
        setCameraStatus(true);
        setStatus('Camera active. Position face in the oval guide.', '');
        setFaceCount('Searching for face...');
        startButton.disabled = true;
        startButton.innerHTML = '📷 Camera Active';
        detectionRunning = true; detectFace();
    } catch(e) {
        setStatus('✗ Camera Access Denied', 'error');
        setFaceCount('Allow camera permission in browser settings.');
        showDebug('Camera error:\n' + e.message);
    }
}

async function detectFace() {
    if (!detectionRunning) return;
    try {
        const result = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.3 })).withFaceLandmarks().withFaceDescriptors();
        const resized = faceapi.resizeResults(result, { width: video.videoWidth, height: video.videoHeight });
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        resized.forEach(d => { const b = d.detection.box; ctx.strokeStyle='#4ade80'; ctx.lineWidth=3; ctx.strokeRect(b.x,b.y,b.width,b.height); });
        if (resized.length === 1) {
            latestFace = resized[0].detection; latestDescriptor = resized[0].descriptor;
            if (!isVerifying) { setStatus('✓ Face Detected — Ready to Verify', 'success'); setFaceCount('1 face detected. Click "Verify My Face" to proceed.'); verifyButton.disabled = false; }
        } else if (resized.length > 1) {
            latestFace = null; latestDescriptor = null;
            if (!isVerifying) { setStatus('⚠ Multiple Faces Detected', 'error'); setFaceCount('Only one person should be in front of the camera.'); verifyButton.disabled = true; }
        } else {
            latestFace = null; latestDescriptor = null;
            if (!isVerifying) { setStatus('Searching for face...', ''); setFaceCount('Position your face within the oval guide.'); verifyButton.disabled = true; }
        }
    } catch(e) { console.error('Detection error:', e); }
    setTimeout(detectFace, 300);
}

verifyButton.addEventListener('click', async function() {
    if (!latestDescriptor) { setStatus('⚠ No face detected. Position your face first.', 'error'); return; }
    if (isVerifying) return;
    isVerifying = true; verifyButton.disabled = true;
    empCard.classList.remove('visible'); attActions.classList.remove('visible');
    verifiedEmployee = null; debugBox.style.display = 'none';
    setStatus('🔍 Verifying identity...', ''); setFaceCount('Comparing face data with registered employees...');
    try {
        const descriptor = JSON.stringify(Array.from(latestDescriptor));
        const verifyPayload = { face_descriptor: descriptor };
        if (currentLat !== null && currentLng !== null) {
            verifyPayload.lat = currentLat;
            verifyPayload.lng = currentLng;
        }
        const r = await fetch('/face-verification/verify', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest' }, body: JSON.stringify(verifyPayload) });
        const responseText = await r.text();
        let data;
        try { data = JSON.parse(responseText); } catch(je) { throw new Error('Invalid server response. HTTP: ' + r.status); }
        if (!r.ok) throw new Error(data.message || 'Server error. HTTP: ' + r.status);
        if (data.success && data.matched) {
            const locName = data.verified_location || verifiedLocationName || '';
            setStatus(locName ? '✓ Location Verified — ' + locName : '✓ Identity Verified Successfully', 'success');
            setFaceCount('Biometric match confirmed' + (locName ? ' at ' + locName : '') + '. Select an action below.');
            const initials = data.employee.name.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2);
            empAvatar.textContent = initials;
            empName.textContent = data.employee.name;
            empIdText.textContent = 'Employee ID: ' + data.employee.employee_id;
            empDist.textContent = 'Confidence: ' + Math.round((1 - data.distance) * 100) + '% match';
            empCard.classList.add('visible');
            verifiedEmployee = data.employee;
            attActions.classList.add('visible');
            await loadAttendanceStatus();
        } else {
            setStatus('✗ Face Not Recognised', 'error');
            setFaceCount(data.message || 'No matching employee found in the system.');
            showDebug('Verification result:\n' + JSON.stringify(data, null, 2));
        }
    } catch(e) {
        console.error(e);
        setStatus('✗ Verification Error', 'error');
        setFaceCount(e.message || 'Unable to verify face.');
        showDebug('Error:\n' + e.message);
    }
    isVerifying = false; verifyButton.disabled = !latestDescriptor;
});

clockInButton.addEventListener('click', async function() {
    if (!verifiedEmployee?.id) return;
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = true;
    clockInButton.textContent = '⏳ Clocking In...';
    setStatus('Saving clock in record...', '');
    debugBox.style.display = 'none';
    try {
        const payload = { employee_id: verifiedEmployee.id };
        if (currentLat !== null && currentLng !== null) { payload.lat = currentLat; payload.lng = currentLng; }
        const r = await fetch('/attendance/clock-in', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest' }, body: JSON.stringify(payload) });
        const responseText = await r.text();
        let d; try { d = JSON.parse(responseText); } catch(e) { throw new Error('Invalid response. HTTP: ' + r.status); }
        if (r.ok && d.success) {
            setStatus('✅ Clock In Successful! Redirecting...', 'success');
            setFaceCount(verifiedEmployee.name + ' is now marked Present at ' + d.attendance.clock_in + '.');
            showDebug('✓ ' + d.message + '\nClock In: ' + d.attendance.clock_in + '\nMethod: ' + d.attendance.verification_method);
            clockInButton.style.display = 'none'; clockOutButton.style.display = 'none'; startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none';
            attendanceState = 'clocked_in';
            setTimeout(() => {
                @auth
                    window.location.href = "{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}";
                @else
                    window.location.reload();
                @endauth
            }, 1500);
        } else { throw new Error(d.message || 'Unable to Clock In.'); }
    } catch(e) { setStatus('✗ Clock In Failed', 'error'); setFaceCount(e.message || 'Unable to Clock In.'); showDebug('Clock In error:\n' + e.message); }
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = false;
    clockInButton.innerHTML = '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg> Clock In';
});

clockOutButton.addEventListener('click', async function() {
    if (!verifiedEmployee?.id) return;
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = true;
    clockOutButton.textContent = '⏳ Clocking Out...';
    setStatus('Saving clock out record...', '');
    debugBox.style.display = 'none';
    try {
        const payload = { employee_id: verifiedEmployee.id };
        if (currentLat !== null && currentLng !== null) { payload.lat = currentLat; payload.lng = currentLng; }
        const r = await fetch('/attendance/clock-out', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest' }, body: JSON.stringify(payload) });
        const responseText = await r.text();
        let d; try { d = JSON.parse(responseText); } catch(e) { throw new Error('Invalid response. HTTP: ' + r.status); }
        if (r.ok && d.success) {
            setStatus('✅ Clock Out Successful! Redirecting...', 'success');
            setFaceCount(verifiedEmployee.name + ' has completed attendance for today.');
            showDebug('✓ ' + d.message + '\nIn: ' + d.attendance.clock_in + '\nOut: ' + d.attendance.clock_out + '\nMethod: ' + d.attendance.verification_method);
            clockOutButton.style.display = 'none'; clockInButton.style.display = 'none'; startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none';
            attendanceState = 'completed';
            setTimeout(() => {
                @auth
                    window.location.href = "{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}";
                @else
                    window.location.reload();
                @endauth
            }, 1500);
        } else { throw new Error(d.message || 'Unable to Clock Out.'); }
    } catch(e) { setStatus('✗ Clock Out Failed', 'error'); setFaceCount(e.message || 'Unable to Clock Out.'); showDebug('Clock Out error:\n' + e.message); }
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = false;
    clockOutButton.innerHTML = '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg> Clock Out';
    if (attendanceState === 'completed') { clockInButton.style.display = 'none'; clockOutButton.style.display = 'none'; startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none'; }
});

startBreakButton.addEventListener('click', async function() {
    if (!verifiedEmployee?.id) return;
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = true;
    startBreakButton.textContent = '⏳ Starting Break...';
    setStatus('Saving break record...', '');
    debugBox.style.display = 'none';
    try {
        const payload = { employee_id: verifiedEmployee.id };
        if (currentLat !== null && currentLng !== null) { payload.lat = currentLat; payload.lng = currentLng; }
        const r = await fetch('/attendance/start-break', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest' }, body: JSON.stringify(payload) });
        const responseText = await r.text();
        let d; try { d = JSON.parse(responseText); } catch(e) { throw new Error('Invalid response. HTTP: ' + r.status); }
        if (r.ok && d.success) {
            setStatus('✅ Break Started! Redirecting...', 'success');
            setFaceCount(verifiedEmployee.name + ' is now on a break.');
            showDebug('✓ ' + d.message + '\nBreak Start: ' + d.attendance.break_start);
            clockInButton.style.display = 'none'; clockOutButton.style.display = 'none'; startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none';
            attendanceState = 'on_break';
            setTimeout(() => {
                @auth
                    window.location.href = "{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}";
                @else
                    window.location.reload();
                @endauth
            }, 1500);
        } else { throw new Error(d.message || 'Unable to Start Break.'); }
    } catch(e) { setStatus('✗ Break Start Failed', 'error'); setFaceCount(e.message || 'Unable to Start Break.'); showDebug('Break error:\n' + e.message); }
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = false;
    startBreakButton.innerHTML = '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Start Break';
});

endBreakButton.addEventListener('click', async function() {
    if (!verifiedEmployee?.id) return;
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = true;
    endBreakButton.textContent = '⏳ Ending Break...';
    setStatus('Saving break end record...', '');
    debugBox.style.display = 'none';
    try {
        const payload = { employee_id: verifiedEmployee.id };
        if (currentLat !== null && currentLng !== null) { payload.lat = currentLat; payload.lng = currentLng; }
        const r = await fetch('/attendance/end-break', { method: 'POST', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest' }, body: JSON.stringify(payload) });
        const responseText = await r.text();
        let d; try { d = JSON.parse(responseText); } catch(e) { throw new Error('Invalid response. HTTP: ' + r.status); }
        if (r.ok && d.success) {
            setStatus('✅ Break Ended! Redirecting...', 'success');
            setFaceCount(verifiedEmployee.name + ' has ended their break.');
            showDebug('✓ ' + d.message + '\nBreak End: ' + d.attendance.break_end);
            clockInButton.style.display = 'none'; clockOutButton.style.display = 'none'; startBreakButton.style.display = 'none'; endBreakButton.style.display = 'none';
            attendanceState = 'clocked_in';
            setTimeout(() => {
                @auth
                    window.location.href = "{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}";
                @else
                    window.location.reload();
                @endauth
            }, 1500);
        } else { throw new Error(d.message || 'Unable to End Break.'); }
    } catch(e) { setStatus('✗ Break End Failed', 'error'); setFaceCount(e.message || 'Unable to End Break.'); showDebug('Break error:\n' + e.message); }
    clockInButton.disabled = clockOutButton.disabled = startBreakButton.disabled = endBreakButton.disabled = false;
    endBreakButton.innerHTML = '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> End Break';
});

startButton.addEventListener('click', startCamera);

// Geolocation Check — Multi-Location Validation (Workplace, Home/Demo, College/Demo)
const allowedLocations = @json(\App\Services\GeolocationService::getActiveLocations());

function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3;
    const p1 = lat1 * Math.PI/180;
    const p2 = lat2 * Math.PI/180;
    const dp = (lat2-lat1) * Math.PI/180;
    const dl = (lon2-lon1) * Math.PI/180;
    const a = Math.sin(dp/2) * Math.sin(dp/2) +
              Math.cos(p1) * Math.cos(p2) *
              Math.sin(dl/2) * Math.sin(dl/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

let currentLat = null;
let currentLng = null;
let verifiedLocationName = null;

if (allowedLocations && allowedLocations.length > 0) {
    startButton.disabled = true;
    setStatus('Checking allowed locations...', '');
    setFaceCount('Please allow GPS location access to verify your attendance area.');
    
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            currentLat = position.coords.latitude;
            currentLng = position.coords.longitude;

            let validLocation = null;
            let nearestLocation = null;
            let minDistance = Infinity;
            let distanceLines = [];

            for (const loc of allowedLocations) {
                const dist = getDistance(currentLat, currentLng, loc.lat, loc.lng);
                const isInside = dist <= loc.radius;
                distanceLines.push('• ' + loc.name + ': ' + Math.round(dist) + 'm (Radius: ' + loc.radius + 'm)' + (isInside ? ' [INSIDE]' : ''));

                if (dist < minDistance) {
                    minDistance = dist;
                    nearestLocation = { ...loc, distance: dist };
                }

                if (isInside && !validLocation) {
                    validLocation = { ...loc, distance: dist };
                }
            }

            if (validLocation) {
                verifiedLocationName = validLocation.name;
                setStatus('✓ Location Verified', 'success');
                setFaceCount('Location Verified: ' + validLocation.name + ' (' + Math.round(validLocation.distance) + 'm away, allowed: ' + validLocation.radius + 'm). Ready for face scan.');
                showDebug('✓ Location Verified: ' + validLocation.name + '\n' + distanceLines.join('\n'));
                loadFaceModels(); // load models only if valid
            } else {
                const nearestDist = Math.round(minDistance);
                const nearestRad = nearestLocation ? Math.round(nearestLocation.radius) : 500;
                const nearestName = nearestLocation ? nearestLocation.name : 'Workplace';
                setStatus('✗ Outside Allowed Location', 'error');
                setFaceCount('Outside Allowed Location: You are outside all allowed locations (Nearest: ' + nearestName + ', ' + nearestDist + 'm away, max allowed: ' + nearestRad + 'm). Attendance is not allowed.');
                showDebug('✗ GPS Outside All Allowed Locations:\n' + distanceLines.join('\n'));
            }
        }, function(error) {
            let errorMsg = 'Location permission denied or cannot be verified.';
            if (error.code === 1) {
                errorMsg = 'Location permission was denied. Please enable GPS in browser settings.';
            } else if (error.code === 2) {
                errorMsg = 'Location information is currently unavailable. Ensure device GPS is turned on.';
            } else if (error.code === 3) {
                errorMsg = 'Location check timed out. Please try again.';
            }
            setStatus('✗ Location Denied', 'error');
            setFaceCount(errorMsg + ' Attendance is not allowed.');
            showDebug('GPS Error (' + error.code + '): ' + error.message);
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
    } else {
        setStatus('✗ Location Error', 'error');
        setFaceCount('Geolocation is not supported by your browser.');
    }
} else {
    // If no config, just proceed
    loadFaceModels();
}
</script>
</body>
</html>