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
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = env('SECRET_KEY');
    }

    public function register(Request $request)
    {
        // الحصول على المدخلات
        $fields = $request->all();

        // التحقق من المدخلات
        $validator = Validator::make($fields, [
            'email' => 'required|email|unique:users,email',
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
            'isValidEmail' => User::IS_INVALID_EMAIL,
            'remember_token' => $this->generateRandomCode(),
        ]);
        NewUserCreated::dispatch($user);


        return response()->json(['user'=>$user,'message' => 'User Created'], 200);
    }


    private function generateRandomCode()
    {
        return Str::random(10) . time();
    }

    public function checkEmail($token){
        User::where('remember_token', $token)
            ->update(['isValidEmail' => User::IS_VALID_EMAIL]);
        return redirect()('/login');
    }

    public function login(Request $request)
    {

        $fields = $request->all();

        $errors = Validator::make($fields, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = User::where('email', $fields['email'])->first();

        if (!is_null($user)) {

            if ((int) $user->isValidEmail !== User::IS_VALID_EMAIL) {
                // إطلاق الحدث لإرسال بريد التحقق
                NewUserCreated::dispatch($user);

                // إعادة الاستجابة
                return response()->json([
                    'message' => 'We sent you an email verification!',
                    'isLoggedIn' => false,
                ], 422);
            }

        }

        if (!$user || !Hash::check($fields['password'], $user->password)) {

            return response(['message' => 'email or password invalid'],422);
        }


        $token = $user->createToken($this->secretKey)->plainTextToken;
        return response(
            [
                'user' => $user,
                'message' => 'loggedin',
                'token' => $token,
                'isLoggedIn' => true

            ],
            200
        );
    }


}
