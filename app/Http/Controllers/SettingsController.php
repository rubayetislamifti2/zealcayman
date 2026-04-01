<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function aboutUs(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'about_us' => 'required',
            ]);

            if($validation->fails()){
                return $this->errorResponse($validation->messages(),'Validation failed',400);
            }

            $aboutUs = AboutUs::updateOrCreate(['id'=>1],['about_us' => $request->about_us]);

            return $this->successResponse($aboutUs,'About Us saved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function getAboutUs(){
        try {
            $aboutUs = AboutUs::first();
            return $this->successResponse($aboutUs,'About Us saved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function termsCondition(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'terms_condition' => 'required',
            ]);

            if($validation->fails()){
                return $this->errorResponse($validation->messages(),'Validation failed',400);
            }

            $aboutUs = TermsCondition::updateOrCreate(['id'=>1],['terms_condition' => $request->terms_condition]);

            return $this->successResponse($aboutUs,'About Us saved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function getTermsCondition(){
        try {
            $aboutUs = TermsCondition::first();
            return $this->successResponse($aboutUs,'About Us saved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function privacyPolicy(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'privacy_policy' => 'required',
            ]);

            if($validation->fails()){
                return $this->errorResponse($validation->messages(),'Validation failed',400);
            }

            $aboutUs = PrivacyPolicy::updateOrCreate(['id'=>1],['privacy_policy' => $request->privacy_policy]);

            return $this->successResponse($aboutUs,'About Us saved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function getPrivacyPolicy(){
        try {
            $aboutUs = PrivacyPolicy::first();
            return $this->successResponse($aboutUs,'About Us saved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
}
