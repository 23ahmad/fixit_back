<?php

namespace App\Traits;

trait FixitTrait
{
    public function SuccessResponse($data=null,$message=null,$code=null)
    {
        // إرجاع استجابة JSON تحتوي على البيانات والرسالة وكود الاستجابة
        $array=[
            'data'=>$data,
            'message'=>$message,
            'code'=>$code
        ];
        return response($array,$code);
    }

    public function ErrorResponse($message=null,$code=null)
    {
        // إرجاع استجابة JSON تحتوي على رسالة الخطأ وكود الاستجابة
        $array=[
            'message'=>$message,
            'code'=>$code
        ];
        return response($array,$code);
    }
}

