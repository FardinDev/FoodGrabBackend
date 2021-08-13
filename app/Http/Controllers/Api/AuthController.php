<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;

class AuthController extends Controller
{

    private $maxOtpCount;

    public function __construct(){
        $this->maxOtpCount = env('MAX_OTP_COUNT', 4);
    }

    public function register(Request $request)
    {

        if ($request->has('phone')) {
            $request->merge(['phone' => '880' . substr($request->phone, -10)]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',

            'phone' => [
                'required',
                'regex:^(?:\+?88)?01[2-9]\d{8}$^',
                Rule::unique('users')->where(function ($query) {
                    $query->where('otp_verified_at', '!=', null);
                }),
            ],
            'password' => 'required|string|min:8',

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }

        try {

            DB::beginTransaction();

            $user = User::where('phone', $request->phone)->first();
            

           
            if ($user) {

                if ($user->otp_count > $this->maxOtpCount) {

                    $user->status = 0;
                    $user->save();
                    return $this->apiFailedResponse('Maximum OTP limit reached. please contact the App administrator');
                }

                $otp = $this->sendOTP($request->phone);

                $user->otp = $otp;
                $user->otp_sent_at = Carbon::now();
                $user->otp_count = $user->otp_count + 1;
                $user->save();

            } else {
                $otp = $this->sendOTP($request->phone);
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'otp' => $otp,
                    'otp_sent_at' => Carbon::now(),
                    'status' => 0,
                    'password' => Hash::make($request->password),
                ]);

            }

            // $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'otp_status' => true,
                'user' => new UserResource($user),
            ];

            DB::commit();
            return $this->apiSuccessResponse('OTP successful', $data);

        } catch (\Throwable $th) {
            DB::rollback();

            return $this->apiFailedResponse('Something went wrong', $th);
        }
    }
    public function resetPassword(Request $request)
    {

        if ($request->has('phone')) {
            $request->merge(['phone' => '880' . substr($request->phone, -10)]);
        }

        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'regex:^(?:\+?88)?01[2-9]\d{8}$^',
                Rule::exists('users')->where(function ($query) {
                    $query->where('otp_verified_at', '!=', null);
                }),
            ],
            'password' => 'required|string|min:8|confirmed',

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }

        try {

            DB::beginTransaction();

            $user = User::where('phone', $request->phone)->first();
        
            $user->password = Hash::make($request->password);
            $user->save();
            
            $data = [
                'reset_status' => true,
            ];

            DB::commit();
            return $this->apiSuccessResponse('Password Reset Successfull. Please login with your new password', $data);

        } catch (\Throwable $th) {
            DB::rollback();

            return $this->apiFailedResponse('Something went wrong', $th);
        }
    }
    public function getOtp(Request $request)
    {

        if ($request->has('phone')) {
            $request->merge(['phone' => '880' . substr($request->phone, -10)]);
        }

        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'regex:^(?:\+?88)?01[2-9]\d{8}$^',
                Rule::exists('users')->where(function ($query) {
                    $query->where('status',  1);
                }),
            ],

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }

        try {

            DB::beginTransaction();

            $user = User::where('phone', $request->phone)->first();
            
            if ($user->otp_count > $this->maxOtpCount) {
                $user->status = 0;
                $user->save();
                return $this->apiFailedResponse('Maximum OTP limit reached. please contact the App administrator');
            }
            
            $otp = $this->sendOTP($request->phone);
            $user->otp = $otp;
            $user->otp_sent_at = Carbon::now();
            $user->otp_count = $user->otp_count + 1;
            $user->save();
      
            // $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'otp_status' => true,
                'user' => new UserResource($user),
            ];

            DB::commit();
            return $this->apiSuccessResponse('OTP successful', $data);

        } catch (\Throwable $th) {
            DB::rollback();

            return $this->apiFailedResponse('Something went wrong', $th);
        }
    }

    public function verify(Request $request)
    {

        if ($request->has('phone')) {
            $request->merge(['phone' => '880' . substr($request->phone, -10)]);
        }

        $validator = Validator::make($request->all(), [

            'phone' => [
                'required',
                'regex:^(?:\+?88)?01[2-9]\d{8}$^',
                Rule::exists('users')->where(function ($query) {
                    $query->where('otp', '!=', null);
                }),
            ],
            'otp' => 'required|string|size:4',

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }

        try {

            DB::beginTransaction();

            $user = User::where('phone', $request->phone)->first();

            if ($user && $user->otp == $request->otp) {

                $user->otp_verified_at = Carbon::now();
                $user->status = 1;
                $user->save();

                $token = $user->createToken('auth_token')->plainTextToken;

                $data = [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user),
                ];
                DB::commit();
                return $this->apiSuccessResponse('Registration Successfull', $data);

            } else {

                DB::rollback();
                return $this->apiFailedResponse('OTP Verification Failed. Please Enter Correct OTP');

            }

        } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiFailedResponse('Something went wrong', $th);
        }
    }

    public function login(Request $request)
    {
        if ($request->has('phone')) {
            $request->merge(['phone' => '880' . substr($request->phone, -10)]);
        }
        $messages = [
            'phone.required' => 'Please enter registered phone number',
            'phone.regex' => 'Provided phone number is invalid',
            'phone.exists' => 'Provided phone number is not registed or Please register to continue',
            'phone' => array(
                'regex' => 'Provided phone number is invalid',
            ),

            'password.required' => 'Please enter valid password',
            'password.invalid' => 'Provided Password is invalid',

        ];
        $validator = Validator::make($request->all(), [
            'phone' => [
                'required',
                'regex:^(?:\+?88)?01[2-9]\d{8}$^',
                Rule::exists('users')->where(function ($query) {
                    $query->where('otp_verified_at', '!=', null)->where('status', 1);
                }),
            ],
            'password' => 'required|string|min:8',

        ], $messages);
        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }

        if (!Auth::attempt($request->only('phone', 'password'))) {

            return $this->apiFailedResponse('Invalid login details');
        }

        $user = User::where('phone', $request->phone)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ];

        return $this->apiSuccessResponse('Login successful', $data);
    }

    public function profile(Request $request)
    {

        $data = [
            'user' => new UserResource(Auth::user()),
        ];

        return $this->apiSuccessResponse('Profile data', $data);
    }


    public function storeToken(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'token' => 'required|string',

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }



        try {

            DB::beginTransaction();

            $user = User::find(auth()->user()->id);

            $user->notification_token = $request->token;
            $user->save();
    
            
            DB::commit();
            
            return $this->apiSuccessResponse('Notification Token Stored Successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiFailedResponse('Something went wrong', $th);
        }


    }

}
