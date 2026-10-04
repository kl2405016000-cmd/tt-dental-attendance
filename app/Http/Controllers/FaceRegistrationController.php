<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FaceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceRegistrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Face Registration Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $employees = Employee::orderBy('name')->get();

        $registrations = FaceRegistration::pluck('employee_id')->toArray();

        return view(
            'face-attendance',
            compact('employees', 'registrations')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Save Face Registration
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'face_image' => [
                'required',
                'string',
            ],

            'face_descriptor' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Face Image
        |--------------------------------------------------------------------------
        */

        $imageData =
            $request->face_image;


        if (!preg_match(
            '/^data:image\/(png|jpeg|jpg);base64,/',
            $imageData
        )) {

            return back()
                ->with(
                    'error',
                    'Invalid face image format.'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Remove Base64 Header
        |--------------------------------------------------------------------------
        */

        $imageData =
            preg_replace(
                '/^data:image\/\w+;base64,/',
                '',
                $imageData
            );


        $imageData =
            str_replace(
                ' ',
                '+',
                $imageData
            );


        /*
        |--------------------------------------------------------------------------
        | Decode Image
        |--------------------------------------------------------------------------
        */

        $image =
            base64_decode(
                $imageData
            );


        if ($image === false) {

            return back()
                ->with(
                    'error',
                    'Unable to process face image.'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Generate File Name
        |--------------------------------------------------------------------------
        */

        $fileName =
            'face_' .
            $request->employee_id .
            '_' .
            time() .
            '.jpg';


        $path =
            'faces/' . $fileName;


        /*
        |--------------------------------------------------------------------------
        | Save Image
        |--------------------------------------------------------------------------
        */

        Storage::disk('public')->put(
            $path,
            $image
        );


        /*
        |--------------------------------------------------------------------------
        | Decode Descriptor for Array Cast
        |--------------------------------------------------------------------------
        */

        $descriptorArray = json_decode($request->face_descriptor, true);
        if (!is_array($descriptorArray)) {
            $descriptorArray = $request->face_descriptor;
        }

        /*
        |--------------------------------------------------------------------------
        | Save or Update Face Registration
        |--------------------------------------------------------------------------
        */

        $existingRegistration = FaceRegistration::where('employee_id', $request->employee_id)->first();
        if ($existingRegistration && $existingRegistration->image_path) {
            Storage::disk('public')->delete($existingRegistration->image_path);
        }

        FaceRegistration::updateOrCreate(
            ['employee_id' => $request->employee_id],
            [
                'image_path' => $path,
                'face_descriptor' => $descriptorArray,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return redirect(
            '/face-attendance'
        )->with(
            'success',
            'Face and face descriptor saved successfully.'
        );
    }

    public function register(Request $request)
    {
        return $this->store($request);
    }
}