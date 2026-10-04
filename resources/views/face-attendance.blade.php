@extends('layouts.app')
@section('title', 'Face Registration - TT Dental Lab')
@section('header_title', 'Face Registration')
@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div class="page-header-title">Biometric Face Registration</div>
        <div class="page-header-sub">Capture employee face references and register biometric profiles for the attendance kiosk</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('face-verification') }}" target="_blank" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
            Open Biometric Kiosk ↗
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
    </div>
</div>

@php
    $totalEmp = $employees->count();
    $registeredCount = count($registrations);
    $unregisteredCount = $totalEmp - $registeredCount;
@endphp

{{-- Stats Row --}}
<div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 20px;">
    <div class="stat-card stat-blue">
        <div class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Total Employees</div>
            <div class="stat-value">{{ $totalEmp }}</div>
            <div class="stat-sub">Active employee records</div>
        </div>
    </div>
    <div class="stat-card stat-green">
        <div class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Face Registered</div>
            <div class="stat-value">{{ $registeredCount }}</div>
            <div class="stat-sub">Biometric profiles active</div>
        </div>
    </div>
    <div class="stat-card stat-amber">
        <div class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
        <div class="stat-info">
            <div class="stat-label">Not Registered</div>
            <div class="stat-value">{{ $unregisteredCount }}</div>
            <div class="stat-sub">Pending biometric setup</div>
        </div>
    </div>
</div>

<div>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Register Employee Biometric Face Profile</div>
                <div style="font-size: 13px; color: var(--text-muted);">Capture employee face reference and compute 128-dimensional facial vectors for biometric verification.</div>
            </div>
        </div>

        <!-- Employee -->
        <div class="form-group">
            <label for="employee_id" class="form-label">
                Select Employee
                <span style="font-size: 11px; font-weight: 500; color: var(--text-muted); margin-left: 6px;">(✓ = face already registered)</span>
            </label>
            <select id="employee_id" class="form-select">
                <option value="">-- Select Employee --</option>
                @foreach ($employees as $employee)
                    @php $isRegistered = in_array($employee->id, $registrations); @endphp
                    <option value="{{ $employee->id }}" {{ $isRegistered ? 'data-registered=1' : '' }}>
                        {{ $isRegistered ? '✓' : '○' }} {{ $employee->employee_id }} — {{ $employee->name }}{{ $isRegistered ? ' (Registered)' : ' (Not Registered)' }}
                    </option>
                @endforeach
            </select>
        </div>


        <!-- Camera -->

        <div class="camera-container">

            <video
                id="camera"
                autoplay
                muted
                playsinline
            ></video>


            <canvas id="overlay"></canvas>


            <div class="scan-box"></div>

        </div>


        <!-- Status -->

        <div
            id="status"
            class="status"
        >

            Loading face detection system...

        </div>


        <div
            id="faceCount"
            class="face-count"
        >

            No face detected.

        </div>


        <!-- Preview -->

        <div
            id="previewContainer"
            class="preview-container"
        >

            <h3>
                Captured Face
            </h3>


            <img
                id="facePreview"
                alt="Captured Face"
            >

        </div>


        <!-- Buttons -->

        <div class="buttons">

            <button
                type="button"
                id="startCamera"
                class="start-button"
                disabled
            >

                📷 Start Camera

            </button>


            <button
                type="button"
                id="captureFace"
                class="capture-button"
                disabled
            >

                📸 Capture & Save Face

            </button>
        </div>


        <!-- Hidden Form -->

        <form
            id="faceForm"
            action="/face-attendance/register"
            method="POST"
            style="display:none;"
        >

            @csrf


            <input
                type="hidden"
                name="employee_id"
                id="formEmployeeId"
            >


            <input
                type="hidden"
                name="face_image"
                id="faceImage"
            >


            <!-- NEW: Face Descriptor -->

            <input
                type="hidden"
                name="face_descriptor"
                id="faceDescriptor"
            >

        </form>


    </div>


</div>
@endsection

@push('styles')
<style>

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            font-weight: bold;

            margin-bottom: 8px;
        }

        select {
            width: 100%;

            padding: 13px;

            border: 1px solid #d1d5db;

            border-radius: 8px;

            background: white;

            font-size: 15px;
        }

        .camera-container {
            position: relative;

            width: 100%;
            max-width: 750px;

            margin: auto;

            background: #111827;

            border-radius: 12px;

            overflow: hidden;
        }

        video {
            display: block;

            width: 100%;
            height: auto;

            transform: scaleX(-1);
        }

        canvas {
            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            transform: scaleX(-1);

            pointer-events: none;
        }

        .scan-box {
            position: absolute;

            width: 280px;
            height: 340px;

            border: 3px solid white;

            border-radius: 45%;

            left: 50%;
            top: 50%;

            transform:
                translate(-50%, -50%);

            pointer-events: none;
        }

        .status {
            margin-top: 20px;

            padding: 15px;

            border-radius: 8px;

            text-align: center;

            font-weight: bold;

            background: #fef3c7;

            color: #92400e;
        }

        .status.detected {
            background: #dcfce7;

            color: #166534;
        }

        .status.error {
            background: #fee2e2;

            color: #991b1b;
        }

        .face-count {
            margin-top: 10px;

            text-align: center;

            color: #6b7280;

            font-size: 14px;
        }

        .buttons {
            margin-top: 25px;

            display: flex;

            justify-content: center;

            gap: 10px;

            flex-wrap: wrap;
        }

        button,
        .back-button {
            border: none;

            padding: 13px 20px;

            border-radius: 8px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;

            text-decoration: none;
        }

        .start-button {
            background: #111827;

            color: white;
        }

        .capture-button {
            background: #166534;

            color: white;
        }

        .capture-button:disabled {
            background: #9ca3af;

            cursor: not-allowed;
        }

        .back-button {
            background: #e5e7eb;

            color: #111827;
        }

        .success-message {
            background: #dcfce7;

            color: #166534;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-weight: bold;

            text-align: center;
        }

        .error-message {
            background: #fee2e2;

            color: #991b1b;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-weight: bold;

            text-align: center;
        }

        .preview-container {
            display: none;

            margin-top: 25px;

            text-align: center;
        }

        #facePreview {
            width: 250px;

            max-width: 100%;

            border-radius: 12px;

            border: 3px solid #166534;
        }

    
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>



    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const video =
        document.getElementById('camera');

    const canvas =
        document.getElementById('overlay');

    const status =
        document.getElementById('status');

    const faceCount =
        document.getElementById('faceCount');

    const startButton =
        document.getElementById('startCamera');

    const captureButton =
        document.getElementById('captureFace');

    const employeeSelect =
        document.getElementById('employee_id');

    /*
    |--------------------------------------------------------------------------
    | Auto-select Employee
    |--------------------------------------------------------------------------
    |
    | When this page is opened from Employee Management using:
    | /face-attendance?employee_id=EMPLOYEE_DATABASE_ID
    | the selected employee will be filled automatically.
    |
    */

    const urlParams =
        new URLSearchParams(window.location.search);

    const selectedEmployeeId =
        urlParams.get('employee_id');

    if (selectedEmployeeId) {

        employeeSelect.value =
            selectedEmployeeId;

    }

    const faceForm =
        document.getElementById('faceForm');

    const formEmployeeId =
        document.getElementById('formEmployeeId');

    const faceImage =
        document.getElementById('faceImage');

    const faceDescriptor =
        document.getElementById('faceDescriptor');

    const facePreview =
        document.getElementById('facePreview');

    const previewContainer =
        document.getElementById('previewContainer');


    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    */

    let cameraStream = null;

    let latestFace = null;

    let latestDescriptor = null;

    let detectionRunning = false;


    /*
    |--------------------------------------------------------------------------
    | Load Face Detection Model
    |--------------------------------------------------------------------------
    */

    async function loadFaceModels()
    {

        try {

            status.textContent =
                'Loading face detection models...';


            const MODEL_URL =
                'https://justadudewhohacks.github.io/face-api.js/models';


            await faceapi.nets.tinyFaceDetector.loadFromUri(
                MODEL_URL
            );


            /*
            Load face landmark model.
            */

            await faceapi.nets.faceLandmark68Net.loadFromUri(
                MODEL_URL
            );


            /*
            Load face recognition model.
            */

            await faceapi.nets.faceRecognitionNet.loadFromUri(
                MODEL_URL
            );


            status.textContent =
                'Face recognition ready. Click Start Camera.';

            status.className =
                'status detected';


            startButton.disabled =
                false;


        } catch (error) {

            console.error(error);


            status.textContent =
                'Unable to load face recognition models. Check your internet connection.';

            status.className =
                'status error';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Start Camera
    |--------------------------------------------------------------------------
    */

    async function startCamera()
    {

        try {

            status.textContent =
                'Requesting camera access...';

            status.className =
                'status';


            cameraStream =
                await navigator.mediaDevices.getUserMedia({

                    video: {

                        facingMode: 'user'

                    },

                    audio: false

                });


            video.srcObject =
                cameraStream;


            await video.play();


            canvas.width =
                video.videoWidth;

            canvas.height =
                video.videoHeight;


            status.textContent =
                'Camera active. Looking for a face...';

            status.className =
                'status';


            detectionRunning =
                true;


            detectFace();


        } catch (error) {

            console.error(error);


            status.textContent =
                'Unable to access camera. Please allow camera permission.';

            status.className =
                'status error';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Face Detection + Descriptor
    |--------------------------------------------------------------------------
    */

    async function detectFace()
    {

        if (!detectionRunning) {

            return;

        }


        try {

            const result =
                await faceapi
                    .detectAllFaces(
                        video,
                        new faceapi.TinyFaceDetectorOptions({
                            inputSize: 320,
                            scoreThreshold: 0.3
                        })
                    )
                    .withFaceLandmarks()
                    .withFaceDescriptors();


            const resizedResults =
                faceapi.resizeResults(
                    result,
                    {
                        width:
                            video.videoWidth,

                        height:
                            video.videoHeight
                    }
                );


            const context =
                canvas.getContext('2d');


            context.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );


            /*
            Draw boxes.
            */

            resizedResults.forEach(
                function (detection)
                {

                    const box =
                        detection.detection.box;


                    context.strokeStyle =
                        '#22c55e';


                    context.lineWidth =
                        5;


                    context.strokeRect(
                        box.x,
                        box.y,
                        box.width,
                        box.height
                    );

                }
            );


            /*
            One face.
            */

            if (resizedResults.length === 1) {

                latestFace =
                    resizedResults[0].detection;


                latestDescriptor =
                    resizedResults[0].descriptor;


                status.textContent =
                    '✓ Face Detected';

                status.className =
                    'status detected';


                faceCount.textContent =
                    '1 face detected. Ready to capture.';


                captureButton.disabled =
                    employeeSelect.value === '';

            }


            /*
            Multiple faces.
            */

            else if (resizedResults.length > 1) {

                latestFace =
                    null;

                latestDescriptor =
                    null;


                status.textContent =
                    'Multiple faces detected';

                status.className =
                    'status error';


                faceCount.textContent =
                    'Please make sure only one person is in front of the camera.';


                captureButton.disabled =
                    true;

            }


            /*
            No face.
            */

            else {

                latestFace =
                    null;

                latestDescriptor =
                    null;


                status.textContent =
                    'Looking for a face...';

                status.className =
                    'status';


                faceCount.textContent =
                    'No face detected.';


                captureButton.disabled =
                    true;

            }


        } catch (error) {

            console.error(error);


            status.textContent =
                'Face recognition error.';

            status.className =
                'status error';

        }


        setTimeout(
            detectFace,
            300
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Employee Selection
    |--------------------------------------------------------------------------
    */

    employeeSelect.addEventListener(
        'change',
        function ()
        {

            if (
                latestDescriptor !== null
            ) {

                captureButton.disabled =
                    employeeSelect.value === '';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Capture Face
    |--------------------------------------------------------------------------
    */

    captureButton.addEventListener(
        'click',
        function ()
        {

            if (!employeeSelect.value) {

                alert(
                    'Please select an employee first.'
                );

                return;

            }


            if (!latestFace) {

                alert(
                    'Please make sure one face is detected.'
                );

                return;

            }


            if (!latestDescriptor) {

                alert(
                    'Face descriptor is not ready. Please wait a moment and try again.'
                );

                return;

            }


            /*
            Face box.
            */

            const box =
                latestFace.box;


            const padding =
                50;


            const x =
                Math.max(
                    box.x - padding,
                    0
                );


            const y =
                Math.max(
                    box.y - padding,
                    0
                );


            const width =
                Math.min(
                    box.width + (padding * 2),
                    video.videoWidth - x
                );


            const height =
                Math.min(
                    box.height + (padding * 2),
                    video.videoHeight - y
                );


            /*
            Create image canvas.
            */

            const captureCanvas =
                document.createElement(
                    'canvas'
                );


            captureCanvas.width =
                width;


            captureCanvas.height =
                height;


            const context =
                captureCanvas.getContext('2d');


            context.drawImage(

                video,

                x,
                y,
                width,
                height,

                0,
                0,
                width,
                height

            );


            /*
            Convert image.
            */

            const imageData =
                captureCanvas.toDataURL(
                    'image/jpeg',
                    0.90
                );


            /*
            Show preview.
            */

            facePreview.src =
                imageData;


            previewContainer.style.display =
                'block';


            /*
            Employee ID.
            */

            formEmployeeId.value =
                employeeSelect.value;


            /*
            Face image.
            */

            faceImage.value =
                imageData;


            /*
            Descriptor.
            */

            faceDescriptor.value =
                JSON.stringify(
                    Array.from(
                        latestDescriptor
                    )
                );


            /*
            Employee name.
            */

            const selectedOption =
                employeeSelect.options[
                    employeeSelect.selectedIndex
                ];


            const employeeName =
                selectedOption.text;


            /*
            Confirm.
            */

            const confirmed =
                confirm(
                    'Save this face for ' +
                    employeeName +
                    '?'
                );


            if (!confirmed) {

                return;

            }


            status.textContent =
                'Saving face and descriptor...';

            status.className =
                'status';


            faceForm.submit();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Start Camera
    |--------------------------------------------------------------------------
    */

    startButton.addEventListener(
        'click',
        startCamera
    );


    /*
    |--------------------------------------------------------------------------
    | Load Models
    |--------------------------------------------------------------------------
    */

    loadFaceModels();



</script>
@endpush
