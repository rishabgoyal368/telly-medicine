<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\Vital;

class VitalController extends Controller
{

    protected $apiControler;
    public function __construct()
    {
        $this->apiControler = new ApiController();
    }

    public function getStaticVital()
    {
        $data = array(
            array(
                'id' => 1,
                'name' => 'Blood pressure'
            ),
            array(
                'id' => 2,
                'name' => 'Blood sugar'
            ),
            array(
                'id' => 3,
                'name' => 'Weight'
            ),
        );
        $code = 200;
        $message =  'Dropdown Vital list';
        $response = $this->apiControler->generateResponse($code, $message, $data);
        return response()->json($response);
    }

    public function getVital(Request $request)
    {
        try {
            $name = $request->name;
            $doctors = Doctor::where(function ($query) use ($name) {
                if ($name) {
                    $query->orwhere('name', 'LIKE', "%{$name}%");
                    $query->orwhere('speciality', 'LIKE', "%{$name}%");
                }
            })
                ->paginate(10);
            foreach ($doctors as $key => $doctor) {
                $doctors[$key]['profile_image'] = env('APP_URL') . 'uploads/doctor/' . $doctor['profile_image'];
            }
            return $doctors;
        } catch (\Exception $e) {
            $code = 404;
            $message =  $e->getMessage();
            $response = $this->apiControler->generateResponse($code, $message);
            return response()->json($response);
        }
    }

    public function addVital(Request $request)
    {
        $data = $request->all();
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make(
            $data,
            [
                'id' => 'nullable',
                'date' => 'required',
                'type' => 'required|in:1,2,3',
                'low_bp' => 'required_if:type,==,1|numeric',
                'high_bp' => 'required_if:type,==,1|numeric',
                'low_sugar' => 'required_if:type,==,2|numeric',
                'high_sugar' => 'required_if:type,==,2|numeric',
                'weight' => 'required_if:type,==,3|numeric',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->apiControler->generateResponse($code, $message);
        } else {
            $data['user_id'] = $user['id'];
            $data['date'] = strtotime(date('d-m-Y', strtotime($data['date'])));
            if ($data['type'] == '1') {
                $a = $data['high_bp'];
                $b = $data['low_bp'];
                if ($a < $b) {
                    return 'error';
                } else {
                    if ($a < 120 && $b < 80) {
                        $data['result'] =  "Normal Blood Pressure";
                    } else if ($a > 119 && $a < 140 || $b > 79 && $b < 90) {
                        $data['result'] =  "Pre-hypertension";
                    } else if ($a > 139 && $a < 160 || $b > 89 && $b < 100) {
                        $data['result'] =  "Stage I high blood pressure-hypertension";
                    } else if ($a > 159 && $a < 181 || $b > 99 && $b < 111) {
                        $data['result'] = "Stage II high blood pressure-hypertension";
                    } else if ($a > 180 || $b > 110) {
                        $data['result'] =  "Hypertensive crisis(where emergency care is required)";
                    }
                }
            } else if ($data['type'] == '2') {
                if ($data['low_sugar'] > 80 && $data['high_sugar'] > 180) {
                    $data['result'] = 'High Sugar';
                } else {
                    $data['result'] = 'Normal Sugar';
                }
            } else {
                $data['result'] = $data['weight'];
            }
            Vital::addEdit($data);
            $code = 200;
            $message = 'Record Added Successfully';
            $response = $this->apiControler->generateResponse($code, $message);
        }
        return response()->json($response);
    }
}
