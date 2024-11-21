<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPCodeForgetPassword;
use App\Mail\OTPMail;
use App\Models\Contractor;

class AuthController extends Controller
{
    use FixitTrait;

    public function register(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'role' =>'required|in:admin,homeowner,contractor',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:10|min:10',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',

            // تحقق من وجود category_id والوصف إذا كان المستخدم contractor
            'category_id' => 'required_if:role,contractor|exists:categories,id',
        ]);

        // رسالة الخطأ الذي حدث في البيانات المدخلة(غير مطابق للشروط التي حددت)
        if ($validator->fails()) {
            return $this->ErrorResponse($validator->errors(),422);
        }

        // إضافة المستخدم الذي نقوم بانشائه في ال database
        $user = User::create([
            'role' => $request->role,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
        ]);

        if ($request->role === 'contractor') {
            // إنشاء سجل contractor وربطه بالمستخدم
            $contractor = Contractor::create([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
            ]);

             // تحميل العلاقة الخاصة بالفئة للcontractor للحصول على اسم الفئة
            $contractor->load('category');

           // تهيئة البيانات لعرضها بشكل مبسط
            $responseData = [
                'role' => $user->role,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'country' => $user->country,
                'city' => $user->city,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
                'id' => $user->id,
                'description' => $contractor->description,
                'category_name' => $contractor->category->category_name,
            ];

            return $this->SuccessResponse($responseData, 'Contractor registered successfully', 201);

        }

        // رسالة النجاح مع عرض بيانات المستخدم الذي تم انشاءه
        return $this->SuccessResponse($user,'User registered successfully',201);
    }



    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        // رسالة الخطأ الذي حدث في البيانات المدخلة(غير مطابق للشروط التي حددت)
        if ($validator->fails()) {
            return $this->ErrorResponse($validator->errors(),422);
        }
        // مطابقة البريد الالكتروني المدخل من قبل المستخدم مع البريد الموجود في ال db
        $user= User::where('email',$request->email)->first();

        // في حال عدم وجود البريد او عدم تطابق كلمة السر المدخلة مع المحفوظة في الداتابيز يعيد رسالة خطأ
        if(!$user || !Hash::check($request->password, $user->password))
        {
            return $this->ErrorResponse('Invalid email or password',401);
        }

        // إنشاء رمز الدخول (token) للمستخدم
        $token = $user->createToken('auth_token')->plainTextToken;

        // إعادة هيكلة البيانات التي تتضمن رمز الدخول ودور المستخدم
        $responseData = [
            'token' => $token,          // رمز الدخول الذي تم إنشاؤه
            'role' => $user->role       // دور المستخدم
        ];
        return $this->SuccessResponse($responseData,'Logged in successfully',200);
    }


}




