<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

use App\Models\Doctor;
use App\Models\BookAppointment;


class DoctorController extends Controller
{

    protected $apiControler;
    public function __construct()
    {
        $this->apiControler = new ApiController();
    }

    public function getDoctors(Request $request)
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

    public function getDateSlot()
    {
        for ($i = 1; $i <= 7; $i++) {
            $day[$i]['strtotime'] = strtotime(Carbon::now()->subDays($i)->format('d-m-Y'));
            $day[$i]['date'] = (Carbon::now()->subDays($i)->format('d-m-Y'));
        }
        $code = 200;
        $message = "Dates slots successfully";
        $data = $day;
        $response = $this->apiControler->generateResponse($code, $message, $data);
        return response()->json($response);
    }

    public function getTimeSlot()
    {
        $period = new CarbonPeriod('09:00', '60 minutes', '18:00');
        $slots = [];
        foreach ($period as $key => $item) {
            $slots[$key]['time'] = $item->format("h:i A");
        }
        $code = 200;
        $message = "Time slots successfully";
        $data = $slots;
        $response = $this->apiControler->generateResponse($code, $message, $data);
        return response()->json($response);
    }

    public function bookAppointment(Request $request)
    {
        try {
            $data = $request->all();
            $user = JWTAuth::parseToken()->authenticate();
            $validator = Validator::make(
                $data,
                [
                    'doctor_id' => 'required|exists:doctors,id',
                    'date' => 'required',
                    'time' => 'required',
                ]
            );
            if ($validator->fails()) {
                $code = 404;
                $message = $validator->errors()->first();
                $response = $this->apiControler->generateResponse($code, $message);
            } else {
                $data['user_id'] = $user['id'];
                $data['date'] = date('d-m-Y', strtotime($request['date']));
                $book = BookAppointment::addEdit($data);
                $code = 200;
                $message =  "Book Appointment Successfully";
                $response = $this->apiControler->generateResponse($code, $message);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $code = 404;
            $message =  $e->getMessage();
            $response = $this->apiControler->generateResponse($code, $message);
            return response()->json($response);
        }
    }

    public function getbookAppointment()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $book = BookAppointment::where('user_id', $user->id)->paginate(10);
            $code = 200;
            $message =  "Book Appointment Successfully";
            $response = $this->apiControler->generateResponse($code, $message, $book);
            return response()->json($response);
        } catch (\Exception $e) {
            $code = 404;
            $message =  $e->getMessage();
            $response = $this->apiControler->generateResponse($code, $message);
            return response()->json($response);
        }
    }
}
