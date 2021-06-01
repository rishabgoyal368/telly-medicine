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
use App\Models\User;
use App\Models\Admin;
use Mail, Hash, Auth;
class ApiController extends Controller
{
    public function user_registration(Request $request)
    {
        $data = $request->all();
        print_r($data);die();
        $validator = Validator::make(
            $request->all(),
            [
                'user_id'           => 'required',
                'user_name'         => 'required',
                'password'          => 'required',
                'confirm_password'  =>'required'
            ]
        );

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 400);
        }

        $check_email_exists = User::where('email', $data['email'])->first();
        if (!empty($check_email_exists)) {
            return response()->json(['error' => 'This Email is already exists.'], 400);
        }


        $user                       = new User();
        $user->first_name           = $data['first_name'];
        $user->last_name            = $data['last_name'];
        $user->email                = $data['email'];
        $user->mobile_number        = $data['mobile_number'];
        $hash_password              = Hash::make($data['password']);
        $user->password             = str_replace("$2y$", "$2a$", $hash_password);
        $user->status               = 'Active';
        if ($user->save()) {
            return response()->json(['success' => true, 'data' => $user], Response::HTTP_OK);
        } else {
            return response()->json(['error' => false, 'data' => 'Something went wrong, Please try again later.']);
        }
    }

    public function genrate_user_id(Request $request){
        $generate_random_id           =  date('YmdHis');
        return response()->json(['success' => true, 'data' => $generate_random_id],200);
    
    }

    public function user_verify(Request $request){
        

        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'type'    => 'required'
            ]
        );

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 400);
        }

        if($data['type'] == 'email'){
            
            $check_email_exists = User::where('email', $request['email'])->first();
            if (!empty($check_email_exists)) {
                return response()->json(['error' => 'Email is already exists.'], 400);
            }

        }else{

            $check_phone_number_exists = User::where('mobile_number', $request['mobile_number'])->first();
            if (!empty($check_phone_number_exists)) {
                return response()->json(['error' => 'Phone Number is already exists.'], 400);
            }

        }

        $generate_random_otp            =   rand(1111, 9999);
        $new_user                       =   new User;
        if($data['type'] == 'email'){
            $new_user->email                =   $data['email'];
            $new_user->is_verified_email    =   'no';
        }else{
            $new_user->mobile_number        =   $data['mobile_number'];
            $new_user->is_verified_phone    =   'no';
        }
        $new_user->secret_key           =   $generate_random_otp;
        $new_user->status               =   'inactive';
        $new_user->type                 =   1;
        $new_user->save();

        if($data['type'] == 'email'){
            $project_name   = env('App_name');
            $email          = $data['email'];
            try {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    Mail::send('emails.user_verify', [ 'otp' =>$generate_random_otp], function ($message) use ($email, $project_name) {
                        $message->to($email, $project_name)->subject('User Email Verification');
                    });
                }
            } catch (Exception $e) {
            }
            return response()->json(['success' => true, 'msg' => 'Otp sent on registered Email-id.','data'=>$generate_random_otp],200);    
        }else{
            return response()->json(['success' => true, 'msg' => 'Otp sent on registered Phone number.','data'=>'5546'],200);   
        }

    }

    public function otp_verify(Request $request){

        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'type'    => 'required',
                'otp'     => 'required'
            ]
        );

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 400);
        }
        
        $new_otp  =  $data['otp'];
        if($data['type'] == 'email'){
            $user_details                       =   User::where('email',$data['email'])->first();
            $new_email                          =   $data['email'];
            if($new_otp  == $user_details['secret_key']){
                $user_details->is_verified_email = 'yes';
                $user_details->secret_key        = NULL;
                $user_details->save();

            }else{
                return response()->json(['error' => 'Otp does not match.'], 400);
            }
        }else{
            $user_details                       =  User::where('mobile_number',$data['mobile_number'])->first();
            $new_mobile_number                  =  data['mobile_number'];
            if($new_otp  == $user_details['secret_key']){
                $user_details->is_verified_phone = 'yes';
                $user_details->secret_key        = NULL;
                $user_details->save();
            }else{
                return response()->json(['error' => 'Otp does not match.'], 400);
            }
        }   
        return response()->json(['success' => true, 'msg' => 'Otp verified successfully.'],200); 
    }

    public function user_login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // print_r('here'); die;

        $validator = Validator::make(
            $request->all(),
            [
                'email'      => 'required|email',
                'password'   => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $token = auth()->attempt($credentials);
        if ($token ) {
            return $this->respondWithToken($token);
        } else {
            $response = ["message" => 'Invalid Details'];
            return response($response, 422);
        }
    }

    public function forgot_password(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'      => 'required|email',
            ]
        );

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 200);
        }


        $check_email_exists = User::where('email', $request['email'])->first();
        if (empty($check_email_exists)) {
            return response()->json(['error' => 'Email not exists.'], 400);
        }


        $check_email_exists->secret_key           =  rand(1111, 9999);
        if ($check_email_exists->save()) {
            $project_name = env('App_name');
            $email = $request['email'];
            try {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    Mail::send('emails.user_forgot_password_api', ['name' => ucfirst($check_email_exists['first_name']) . ' ' . $check_email_exists['last_name'], 'otp' => $check_email_exists['secret_key']], function ($message) use ($email, $project_name) {
                        $message->to($email, $project_name)->subject('User Forgot Password');
                    });
                }
            } catch (Exception $e) {
            }
            return response()->json(['success' => true, 'data' => 'Email sent on registered Email-id.'], Response::HTTP_OK);
        } else {
            return response()->json(['error' => false, 'data' => 'Something went wrong, Please try again later.']);
        }
    }

    public function reset_password(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'secret_key'       =>  'required|numeric',
                'email'      => 'required|email',
                'password'   => 'required',
                'confirm_password' => 'required_with:password|same:password'
            ]
        );

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 200);
        }


        $email = $data['email'];
        $check_email = User::where('email', $email)->first();
        if (empty($check_email['secret_key'])) {
            return response()->json(['error' => 'Something went wrong, Please try again later.']);
        }
        if (empty($check_email)) {
            return response()->json(['error' => 'This Email-id is not exists.']);
        } else {
            if ($check_email['secret_key'] == $data['secret_key']) {
                $hash_password                  = Hash::make($data['password']);
                $check_email->password          = str_replace("$2y$", "$2a$", $hash_password);
                $check_email->secret_key               = null;
                if ($check_email->save()) {
                    return response()->json(['success' => true, 'message' => 'Password changed successfully.']);
                } else {
                    return response()->json(['error' => 'Something went wrong, Please try again later.']);
                }
            } else {
                return response()->json(['error' => 'secret_key mismatch']);
            }
        }
    }

    public function profile(Request $request)
    {

        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => $e->getMessage()], 200);
        }

        return response()->json(['success' => true, 'data' => $user], 200);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'mobile_number' => 'required|numeric'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        $user_id = Auth::User()->id;
        $update_profile =  User::where('id',$user_id)->first();
        $update_profile->first_name         = $data['first_name'];
        $update_profile->mobile_number      = $data['mobile_number'];
        if ($update_profile->save()) {
            return response()->json(['success' => true, 'data' => 'User Profile Updated Successfully.'], Response::HTTP_OK);
        }else{
             return response()->json(['error' => 'Something went wrong, Please try again later.']);
        }

    }

 
    public function logout(Request $request)
    {
        print_r($request);die;
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    


    public function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'code' => 200,
            'expire_in' => auth()->factory()->getTTL() * 60
        ]);
    }}
