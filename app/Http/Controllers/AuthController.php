<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->successResponse($validator->errors()->first(),'Validation error',422);
            }

            $data = $validator->validated();

            $otp = rand(1000,9999);
            $otpExpire = Carbon::now()->addMinutes(10);

            $data['otp'] = $otp;
            $data['expired_at'] = $otpExpire;

            $register = User::create($data);
            return $this->successResponse($register,'Registration successful',201);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
                'email' => 'required|string|email|max:255|exists:users,email',
            ]);
            if ($validator->fails()) {
                return $this->successResponse($validator->errors()->first(),'Validation error',422);
            }

            $check = User::where('email',$request->email)
                ->where('otp',$request->otp)
                ->first();

            if (!$check) {
                return $this->successResponse('Invalid OTP','Invalid OTP',422);
            }
            if (Carbon::parse($check->expired_at) < Carbon::now()) {
                return $this->successResponse('OTP Expired','OTP Expired',422);
            }

            $check->is_verified = true;
            $check->email_verified_at = Carbon::now();
            $check->save();

            return $this->successResponse($check,'OTP verified successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
            ]);
            if ($validator->fails()) {
                return $this->successResponse($validator->errors()->first(),'Validation error',422);
            }

            $otp = rand(1000,9999);
            $otpExpire = Carbon::now()->addMinutes(10);

            $user = User::where('email',$request->email)->first();

            $user->otp = $otp;
            $user->otp_expire = $otpExpire;
            $user->save();
            return $this->successResponse($otp,'OTP send successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->successResponse($validator->errors()->first(),'Validation error',422);
            }
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Invalid Credentials','Invalid Credentials', 401);
            }

            $user = JWTAuth::user();

            if ($user->is_verified === false) {
                return $this->errorResponse('Your account is not verified.','Your account is not verified.', 401);
            }

            return $this->successResponse(['token' => $token, 'user' => $user], 'Login successful', 200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'current_password'=>'required|string|min:6',
                'password'=>'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(),'Validation Error',422);
            }
            $user = Auth::user();
            if (!Hash::check($request->current_password,$user->password)){
                return $this->errorResponse('Your password dose not match with your current password','Password Error',422);
            }

            $user->password = $request->password;
            $user->save();
            Auth::logout();
            return $this->successResponse($user,'Password Changed Successfully. Please Login Again',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function forgetPasswordOTPSend(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'email'=>'required|string|email|exists:users,email',
            ],[
                'email.exits'=>'This email is not store in our database'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(),'Validation Error',422);
            }

            $email = User::where('email',$request->email)->first();
            $otp = rand(1000,9999);
            $otpExpire = Carbon::now()->addMinutes(10);

            $email->otp = $otp;
            $email->otp_expire = $otpExpire;
            $email->save();

            return $this->successResponse($email,'OTP Send successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'email'=>'required|string|email|exists:users,email',
                'otp'=>'required|string',
                'password'=>'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(),'Validation Error',422);
            }

            $check = User::where('email',$request->email)->where('otp',$request->otp)->first();

            $check->password = $request->password;
            $check->otp = null;
            $check->otp_expire = null;
            $check->save();

        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            Auth::logout();

            return $this->successResponse(Auth::user(),'Logged out successfully',200);
        }
        catch (TokenExpiredException $exception){
            return $this->errorResponse('Token expired',$exception->getMessage(),500);
        }
        catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
}
