<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\User;

class RecordController extends Controller
{

    protected $apiControler;
    public function __construct()
    {
        $this->apiControler = new ApiController();
    }

    public function addRecord(Request $request)
    {
        try {
            $data = $request->all();
            $user = JWTAuth::parseToken()->authenticate();
            $data['id'] = $user->id;
            $validator = Validator::make(
                $data,
                [
                    'user_id' => 'required|unique:users,user_id,' . $data['id'] . ',id,deleted_at,NULL',
                    'user_name' => 'required',
                    'age' => 'required',
                    'mobile_number' => 'required',
                    'email' => 'nullable|email|unique:users,email,' . $data['id'] . ',id,deleted_at,NULL',
                    'hmo_id_no' => 'required',
                    'hmo_id_name' => 'required',
                    'gender' => 'required',
                    'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                ]
            );
            if ($validator->fails()) {
                $code = 404;
                $message = $validator->errors()->first();
                $response = $this->apiControler->generateResponse($code, $message);
            } else {
                if ($request->profile_image) {
                    $fileName = time() . '.' . $request->profile_image->extension();
                    $request->profile_image->move(public_path('uploads/users'), $fileName);
                    $data['profile_image'] = $fileName;
                } else {
                    $data['profile_image'] = $user['profile_image'];
                }
                $data['status'] = $user['status'];
                $user =  User::addEdit($data);
                $code = 200;
                $message = 'Record Added Successfully';
            }
            $response = $this->apiControler->generateResponse($code, $message);

            return response()->json($response);
        } catch (\Exception $e) {
            $code = 404;
            $message =  $e->getMessage();
            $response = $this->apiControler->generateResponse($code, $message);
            return response()->json($response);
        }
    }

    public function getRecords(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $user['profile_image'] = $user->getProfileImage();
            $code = 404;
            $message = 'User Record';
            $response = $this->apiControler->generateResponse($code, $message, $user);
            return response()->json($response);
        } catch (\Exception $e) {
            $code = 404;
            $message =  $e->getMessage();
            $response = $this->apiControler->generateResponse($code, $message);
            return response()->json($response);
        }
    }
}
