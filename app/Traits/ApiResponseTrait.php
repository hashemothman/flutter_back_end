<?php
namespace App\Traits;


trait ApiResponseTrait{
    public function apiResponse($data= null , $message = null , $status= null){
        $array = [
            'data' =>$data,
            'message'=>$message,
        ];
        return response($array,$status);
    }
}
