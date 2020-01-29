<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $msg = null, $code = 200)
    {
        $array = [
            "status" => in_array($code, $this->successCode()) ? true : false,
            "msg" => $msg,
            "data" => $data,
        ];

        return response()->json($array);
    }

    public function successCode()
    {
        return [200, 201, 202];
    }

    public function paginateNumber()
    {
        return 10;
    }

    public function apiValidation($request, $array)
    {
        $validator = Validator::make($request->all(), $array);

        if($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }

    }

    public function createdResponse($post)
    {
        return $this->apiResponse($post, null, 201);
    }

    public function unKnownError()
    {
        return $this->apiResponse(null, "UnKnown Error", 404);
    }

}