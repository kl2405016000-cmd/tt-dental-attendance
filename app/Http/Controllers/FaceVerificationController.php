<?php

namespace App\Http\Controllers;

use App\Models\FaceRegistration;
use Illuminate\Http\Request;

class FaceVerificationController extends Controller
{
    public function index()
    {
        return view('face-verification');
    }

    public function verify(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate incoming request
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'face_descriptor' => 'required|string',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Decode incoming face descriptor
        |--------------------------------------------------------------------------
        */

        $inputDescriptor = json_decode(
            $request->input('face_descriptor'),
            true
        );


        if (!is_array($inputDescriptor)) {

            return response()->json([
                'success' => false,
                'matched' => false,
                'message' => 'Invalid face descriptor received.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Convert values to float
        |--------------------------------------------------------------------------
        */

        $inputDescriptor = array_values(
            array_map(
                'floatval',
                $inputDescriptor
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Check descriptor length
        |--------------------------------------------------------------------------
        */

        if (count($inputDescriptor) !== 128) {

            return response()->json([
                'success' => false,
                'matched' => false,
                'message' =>
                    'Invalid face descriptor. Expected 128 values.',
                'received_length' =>
                    count($inputDescriptor),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Get registered faces
        |--------------------------------------------------------------------------
        */

        $registrations = FaceRegistration::with('employee')
            ->whereNotNull('face_descriptor')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | No registered faces
        |--------------------------------------------------------------------------
        */

        if ($registrations->isEmpty()) {

            return response()->json([
                'success' => false,
                'matched' => false,
                'message' =>
                    'No registered face found in database.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Find closest face
        |--------------------------------------------------------------------------
        */

        $bestMatch = null;

        $bestDistance = PHP_FLOAT_MAX;


        foreach ($registrations as $registration) {

            $registeredDescriptor =
                $registration->face_descriptor;


            /*
            |--------------------------------------------------------------------------
            | Handle JSON string
            |--------------------------------------------------------------------------
            */

            if (is_string($registeredDescriptor)) {

                $registeredDescriptor =
                    json_decode(
                        $registeredDescriptor,
                        true
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Handle array
            |--------------------------------------------------------------------------
            */

            if (!is_array($registeredDescriptor)) {
                continue;
            }


            $registeredDescriptor =
                array_values(
                    array_map(
                        'floatval',
                        $registeredDescriptor
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | Descriptor must contain 128 values
            |--------------------------------------------------------------------------
            */

            if (
                count($registeredDescriptor) !== 128
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate Euclidean distance
            |--------------------------------------------------------------------------
            */

            $sum = 0.0;


            for (
                $i = 0;
                $i < 128;
                $i++
            ) {

                $difference =
                    $inputDescriptor[$i]
                    -
                    $registeredDescriptor[$i];


                $sum +=
                    $difference *
                    $difference;
            }


            $distance =
                sqrt($sum);


            /*
            |--------------------------------------------------------------------------
            | Keep closest match
            |--------------------------------------------------------------------------
            */

            if (
                $distance < $bestDistance
            ) {

                $bestDistance =
                    $distance;

                $bestMatch =
                    $registration;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | No valid registered descriptor
        |--------------------------------------------------------------------------
        */

        if ($bestMatch === null) {

            return response()->json([
                'success' => false,
                'matched' => false,
                'message' =>
                    'No valid registered face descriptor found.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Matching threshold
        |--------------------------------------------------------------------------
        */

        $threshold = 0.60;


        /*
        |--------------------------------------------------------------------------
        | Face not recognised
        |--------------------------------------------------------------------------
        */

        if (
            $bestDistance > $threshold
        ) {

            return response()->json([
                'success' => false,
                'matched' => false,
                'message' =>
                    'Face not recognised.',
                'distance' =>
                    round(
                        $bestDistance,
                        6
                    ),
                'threshold' =>
                    $threshold,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Employee missing
        |--------------------------------------------------------------------------
        */

        if (
            !$bestMatch->employee
        ) {

            return response()->json([
                'success' => false,
                'matched' => false,
                'message' =>
                    'Face matched but employee record was not found.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify logged-in staff matches face
        |--------------------------------------------------------------------------
        */

        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isStaff()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $loggedInEmployee = \App\Models\Employee::where('user_id', $user->id)->first()
                ?? \App\Models\Employee::where('email', $user->email)->first()
                ?? \App\Models\Employee::where('name', $user->name)->first();

            if (!$loggedInEmployee) {
                return response()->json([
                    'success' => false,
                    'matched' => false,
                    'message' => 'Your staff account is not linked to any employee profile.',
                ]);
            }

            if ($bestMatch->employee->id !== $loggedInEmployee->id) {
                return response()->json([
                    'success' => false,
                    'matched' => false,
                    'message' => 'Face matched to a different employee. You can only record attendance for yourself.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'matched' => true,

            'message' =>
                'Face recognised successfully.',

            'employee' => [

                'id' =>
                    $bestMatch->employee->id,

                'employee_id' =>
                    $bestMatch->employee->employee_id,

                'name' =>
                    $bestMatch->employee->name,

            ],

            'distance' =>
                round(
                    $bestDistance,
                    6
                ),

            'threshold' =>
                $threshold,

        ]);
    }
}