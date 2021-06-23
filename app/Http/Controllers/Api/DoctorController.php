<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\Doctor;

class DoctorController extends Controller
{

    protected $apiControler;
    public function __construct()
    {
        $this->apiControler = new ApiController();
    }

    public function getDoctors()
    {
        try {
            $doctors = Doctor::paginate(10);
            foreach ($doctors as $key => $doctor) {
                $doctors[$key]['profile_image'] = env('APP_URL').'uploads/doctor/' . $doctor['profile_image'] ;  
            }
            return $doctors;
        } catch (\Exception $e) {
            $code = 404;
            $message =  $e->getMessage();
            $response = $this->apiControler->generateResponse($code, $message);
            return response()->json($response);
        }
    }
}
