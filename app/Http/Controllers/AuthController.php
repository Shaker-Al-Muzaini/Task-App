<?php

namespace App\Http\Controllers;

use App\Events\NewUserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // الحصول على المدخلات
        $fields = $request->all();

        // التحقق من المدخلات
        $validator = Validator::make($fields, [
            'email' => 'required|email',
            'password' => 'required|min:6|max:81', // استخدام "min" بدلاً من "main"
        ]);


        // التحقق إذا كانت هناك أخطاء
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        // إنشاء المستخدم
        $user=User::create([
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'isValidEmail' => User::IS_VALID_EMAIL,
            'remember_token' => $this->generateRandomCode(),
        ]);
        NewUserCreated::dispatch($user);

        // رد النجاح
        return response()->json(['message' => 'User Created'], 200);
    }

    // دالة لتوليد رمز عشوائي
    private function generateRandomCode()
    {
        return Str::random(10) . time();
    }
}
