<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $profile = Auth::user();

            return $this->successResponse($profile,'Get profile successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',400);
        }
    }

    public function updateProfile(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'sometimes',
                'email' => 'sometimes|unique:users,email',
            ],[
                'email.unique' => 'Sorry this email already exists',
            ]);

            if($validator->fails()){
                return $this->errorResponse($validator->errors()->first(),'Validation Error',400);
            }

            $data = $validator->validated();

            $profile = Auth::user();

            $profile->update($data);

            return $this->successResponse($profile,'Profile updated successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',400);
        }
    }

    public function updateImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp',
            ]);
            if($validator->fails()){
                return $this->errorResponse($validator->errors()->first(),'Validation Error',400);
            }
            $data = $validator->validated();
            $profile = Auth::user();
            $path = $this->uploadFile($request->file('image'),'profile/',$profile->image);

            $profile->update(['image' => $path]);
            return $this->successResponse($profile,'Profile updated successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',400);
        }
    }
}
