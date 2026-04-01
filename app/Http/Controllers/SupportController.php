<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function createSupport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject'=>'required',
                'message'=>'required',
                'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);
            if($validator->fails()){
                return $this->errorResponse($validator->errors()->first(),'Validation Error',422);
            }

            $data = $validator->validated();
            if($request->hasFile('image')){
                $path = $this->uploadFile($request->file('image'),'support/');
                $data['image'] = $path;
            }
            $support = Support::create($data);

            return $this->successResponse($support,'Support created successfully',201);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
    public function getSupports(Request $request)
    {
        try {
            $search = $request->query('search');

            $supports = Support::with('user')->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })->latest()->paginate(10);
            return $this->successResponse($supports,'Supports retrieved successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
    public function updateSupport(Request $request,$support_id){
        try {
            $support = Support::find($support_id);
            $validator = Validator::make($request->all(), [
                'subject'=>'sometimes',
                'message'=>'sometimes',
                'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);

            if($validator->fails()){
                return $this->errorResponse($validator->errors()->first(),'Validation Error',422);
            }

            $data = $validator->validated();

            if($request->hasFile('image')){
                $path = $this->uploadFile($request->file('image'),'support/',$support->image);
                $data['image'] = $path;
            }
            $support->update($data);

            return $this->successResponse($support,'Support updated successfully',201);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function replySupport(Request $request,$support_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reply'=>'required',
            ]);

            if($validator->fails()){
                return $this->errorResponse($validator->errors()->first(),'Validation Error',422);
            }

            $data = $validator->validated();
            $support = Support::with('user')->find($support_id);
            $support->update([
                'status'=>'replied'
            ]);

            Mail::raw($data['reply'], function ($message) use ($support) {
                $message->to($support->user->email)->subject('Support replied');
            });
            return $this->successResponse($support,'Support replied successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function deleteSupport(Request $request,$support_id){
        try {
            $support = Support::find($support_id);
            $support->delete();
            return $this->successResponse($support,'Support deleted successfully',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
}
