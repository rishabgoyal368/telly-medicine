<?php

namespace App\Http\Controllers;

use JWTAuth;
use Validator;
use IlluminateHttpRequest;
use AppHttpRequestsRegisterAuthRequest;
use TymonJWTAuthExceptionsJWTException;
use SymfonyComponentHttpFoundationResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Admin;
use App\Models\UserProfile;
use Mail, Hash, Auth;
use stdClass;

class ApiController extends Controller
{

    public function generateResponse($code, $message, $data = array())
    {
        $response['code'] = $code;
        $response['message'] = $message;
        $response['data'] = $data;
        return $response;
    }

    public function user_registration(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required',
                'user_name' => 'required',
                'password' => 'required',
                'confirm_password' => 'required',
                'email' => 'nullable|email|unique:users,email,Null,id,deleted_at,NULL',
                'mobile_number' => 'nullable|unique:users,mobile_number,Null,id,deleted_at,NULL',
                'signup_type' => 'required|in:email,facebook,google',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
        } else {
            $data['password'] = Hash::make($data['password']);
            $data['status'] = 'Active';
            $user =  User::addEdit($data);
            if ($user) {
                $url = url('/user-verify');
                // Mail::send('emails.user_verify', ['otp' => $url], function ($message) use ($data) {
                //     $message->to($data['email'], env('App_name'))->subject('User Email Verification');
                // });
                $code = 200;
                $message = 'User Register Successfully';
                $data = $user;
                $response = $this->generateResponse($code, $message, $data);
            } else {
                $code = 404;
                $message = 'Something went wrong, Please try again later';
                $response = $this->generateResponse($code, $message);
            }
        }
        return response()->json($response);
    }

    public function user_login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'nullable|email',
                'mobile_number' => 'nullable',
                'password' => 'required'
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
        } else {
            if (@$request->email) {
                $credentials = $request->only('email', 'password');
            } else if (@$request->mobile_number) {
                $credentials = $request->only('mobile_number', 'password');
            }
            $token = auth()->attempt($credentials);
            $type = 'patient';
            if (!$token) {
                $type = 'doctor';
                $token = auth('doctor')->attempt($credentials);
            }
            if ($token) {
                $data = new \stdClass();
                $data->token =  $token;
                $data->type =  $type;
                $code = 200;
                $message = 'Successfull Login';
                $response = $this->generateResponse($code, $message, $data);
            } else {
                $code = 404;
                $message = 'Invalid Details';
                $response = $this->generateResponse($code, $message);
            }
        }
        return response()->json($response);
    }

    public function genrate_user_id(Request $request)
    {
        $random = Str::random(7);
        $data = user::where('user_id', $random)->count() ? $random . strtotime(date('d-m-Y')) : $random;
        $code = 200;
        $message = 'Generate user id';
        $response = $this->generateResponse($code, $message, $data);
        return response()->json($response);
    }

    public function user_verify(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'type'    => 'required|in:email,mobile_number',
                'email'      => 'nullable|required_if:type,==,email|email|exists:users,email',
                'mobile_number'      => 'nullable|required_if:type,==,mobile_number|numeric|exists:users,mobile_number',
            ]
        );

        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
        } else {
            $generate_random_otp =   rand(1111, 9999);

            $db_key = @$request['email'] ? array('email', 'is_verified_email') : array('mobile_number', 'is_verified_phone');
            $db_value = @$request['email'] ? $request['email'] : $request['mobile_number'];

            User::where($db_key[0], $db_value)->update([
                $db_key[0] => $db_value,
                $db_key[1] => 'no',
                'secret_key' => $generate_random_otp,
                'status' => User::STATUSACTIVE
            ]);

            if ($data['type'] == 'email') {
                $email = $data['email'];
                try {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        Mail::send('emails.user_verify', ['otp' => $generate_random_otp], function ($message) use ($email) {
                            $message->to($email, env('App_name'))->subject('User Email Verification');
                        });
                    }
                } catch (Exception $e) {
                }
            }
            if ($data['mobile_number']) {
                #TODO TWILLO INTEGRATE
            }
            $data =  $generate_random_otp;
            $code = 200;
            $message = 'OTP generate successfully';
            $response = $this->generateResponse($code, $message, $data);
        }
        return response()->json($response);
    }

    public function otp_verify(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        } else {
            $email = $data['email'];
            $check_email = User::where('email', $email)->first();
            if (empty($check_email['secret_key'])) {
                $code = 404;
                $message = 'Something went wrong, Please try again later';
                $response = $this->generateResponse($code, $message);
                return response()->json($response);
            }
            if ($check_email['secret_key'] == $data['otp']) {
                $code = 200;
                $message = 'OTP is matched';
                $response = $this->generateResponse($code, $message);
                return response()->json($response);
            } else {
                $code = 400;
                $message = 'OTP is wrong';
                $response = $this->generateResponse($code, $message);
                return response()->json($response);
            }
        }
    }

    public function forgot_password(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'      => 'required|email|exists:users,email',
            ]
        );

        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
        } else {
            $check_email_exists = User::where('email', $request['email'])->first();
            $check_email_exists->secret_key  =  rand(1111, 9999);
            if ($check_email_exists->save()) {
                $project_name = env('App_name');
                $email = $request['email'];
                try {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        Mail::send('emails.user_forgot_password_api', ['name' => ucfirst($check_email_exists['user_name']), 'otp' => $check_email_exists['secret_key']], function ($message) use ($email, $project_name) {
                            $message->to($email, $project_name)->subject('User Forgot Password');
                        });
                    }
                } catch (Exception $e) {
                }
                $code = 200;
                $message = 'OTP sent on registered Email-id';
                $response = $this->generateResponse($code, $message);
            } else {
                $code = 404;
                $message = 'Something went wrong, Please try again later';
                $response = $this->generateResponse($code, $message);
            }
        }
        return response()->json($response);
    }

    public function reset_password(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'secret_key' =>  'required',
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
                'confirm_password' => 'required_with:password|same:password'
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        }
        $email = $data['email'];
        $check_email = User::where('email', $email)->first();
        if (empty($check_email['secret_key'])) {
            $code = 404;
            $message = 'Something went wrong, Please try again later';
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        }
        if ($check_email['secret_key'] == $data['secret_key']) {
            $hash_password = Hash::make($data['password']);
            $check_email->password = $hash_password;
            $check_email->secret_key = null;
            if ($check_email->save()) {
                $code = 200;
                $message = 'Password changed successfully';
                $response = $this->generateResponse($code, $message);
                return response()->json($response);
            } else {
                $code = 404;
                $message = 'Something went wrong, Please try again later';
                $response = $this->generateResponse($code, $message);
                return response()->json($response);
            }
        } else {
            $code = 400;
            $message = 'OTP is wrong';
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        }
    }


    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate(JWTAuth::getToken());
            $data =  [];
            $code = 200;
            $message = 'User logged out successfully';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        } catch (JWTException $exception) {
            $data =  [];
            $code = 404;
            $message = 'Sorry, the user cannot be logged out';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        }
    }

    public function respondWithToken($token)
    {
        return $token;
    }

    public function createProfile(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $data,
            [
                'body_fat' =>  'required',
                'hmo_id' => 'nullable',
                'hmo_id_doc' => 'nullable|max:2048',
                'relationship' => 'required',
                'kin_name' => 'required',
                'kin_number' => 'required',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            if ($request->hmo_id_doc) {
                $fileName = time() . '.' . $request->hmo_id_doc->extension();
                $request->hmo_id_doc->move(public_path('uploads/users'), $fileName);
                $user->hmo_id_doc = $fileName;
            } else {
                $user->hmo_id_doc = $user['hmo_id_doc'];
            }
            $user->body_fat = $data['body_fat'];
            $user->hmo_id = $data['hmo_id'];
            $user->relationship = $data['relationship'];
            $user->kin_name = $data['kin_name'];
            $user->kin_number = $data['kin_number'];
            $user->save();
            $data =  [];
            $code = 200;
            $message = 'Profile Updated successfully';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        }
    }

    public function createProfile2(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $data,
            [
                'blood_group' => 'required',
                'genotype' => 'required',
                'is_smoking' => 'required',
                'is_alcohol' => 'required',
                'is_diet' => 'required',
                'last_medical_checkup' => 'nullable',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            UserProfile::create([
                'user_id' => @$user['id'],
                'blood_group' => @$request->blood_group,
                'genotype' => @$request->genotype,
                'is_smoking' => @$request->is_smoking,
                'is_alcohol' => @$request->is_alcohol,
                'is_diet' => @$request->is_diet,
                'last_medical_checkup' => @$request->last_medical_checkup,
            ]);

            $data =  [];
            $code = 200;
            $message = 'Profile Updated successfully';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        }
    }

    public function createProfile3(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $data,
            [
                'antibiotics' => 'required',
                'blood_presure' => 'required',
                'antacid' => 'required',
                'hormone_therapy' => 'required',
                'anti_asthma' => 'required',
                'arpirin' => 'required',
                'diet_pill' => 'required',
                'supplement' => 'required',
                'herbal_product' => 'required',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            UserProfile::where('user_id', $user['id'])->update([
                'antibiotics' => @$request->antibiotics,
                'blood_presure' => @$request->blood_presure,
                'antacid' => @$request->antacid,
                'hormone_therapy' => @$request->hormone_therapy,
                'anti_asthma' => @$request->anti_asthma,
                'arpirin' => @$request->arpirin,
                'diet_pill' => @$request->diet_pill,
                'supplement' =>  @$request->supplement,
                'herbal_product' =>  @$request->herbal_product,
            ]);

            $data =  [];
            $code = 200;
            $message = 'Profile Updated successfully';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        }
    }

    public function createProfile4(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $data,
            [
                'exercise_level' => 'required',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            UserProfile::where('user_id ', $user['id'])->update([
                'exercise_level' => @$request->antibiotics,
            ]);

            $data =  [];
            $code = 200;
            $message = 'Profile Updated successfully';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        }
    }

    public function createProfile5(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $data,
            [
                'is_any_disease' => 'required',
            ]
        );
        if ($validator->fails()) {
            $code = 404;
            $message = $validator->errors()->first();
            $response = $this->generateResponse($code, $message);
            return response()->json($response);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            UserProfile::where('user_id', $user['id'])->update([
                'is_any_disease' => is_array($request->is_any_disease) ? serialize($request->is_any_disease) : @$request->is_any_disease,
            ]);

            $data =  [];
            $code = 200;
            $message = 'Profile Updated successfully';
            $response = $this->generateResponse($code, $message, $data);
            return response()->json($response);
        }
    }
}
