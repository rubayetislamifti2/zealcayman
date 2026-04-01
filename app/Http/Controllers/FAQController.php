<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    public function getFAQ()
    {
        try {
            $faqs = FAQ::latest()->paginate(10);
            return $this->successResponse($faqs,'FAQ List',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }
    public function createFaq(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(),'Something went wrong',400);
            }

            $data = $validator->validated();

            $faq = FAQ::create($data);

            return $this->successResponse($faq,'FAQ created successfully',200);
        }catch (\Exception $e){
            return $this->errorResponse($e->getMessage(),'Something went wrong',500);
        }
    }

    public function updateFaq(Request $request,$id){
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'sometimes',
                'answer' => 'sometimes'
            ]);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(),'Something went wrong',400);
            }
            $data = $validator->validated();

            $faq = FAQ::find($id);

            $faq->update($data);

            return $this->successResponse($faq,'FAQ updated successfully',200);
        }catch (\Exception $e){
            return $this->errorResponse($e->getMessage(),'Something went wrong',500);
        }
    }

    public function deleteFaq($id){
        try {
            $faq = FAQ::destroy($id);
            return $this->successResponse($faq,'FAQ deleted successfully',200);
        }catch (\Exception $e){
            return $this->errorResponse($e->getMessage(),'Something went wrong',500);
        }
    }
}
