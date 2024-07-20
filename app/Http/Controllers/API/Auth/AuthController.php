<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request, Response $response)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required|string|max:200',
            'email'=> 'required|string|unique:users|email',
            'password' => 'required|string|min:8'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), $response::HTTP_BAD_REQUEST);
        }
        
        try {
            //code...
            $user = User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = $user->createToken('register_token')->plainTextToken;
            return response()->json([
                'status'=> $response::HTTP_OK,
                'data' => $user,
                'access_token' => $token,
                'type' => 'Bearer'
            ], $response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json([
                'status'=> $response::HTTP_INTERNAL_SERVER_ERROR,
                'messafge'=>'Error '. $ex->getMessage()
            ],$response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }  
    public function login(Request $request, Response $response)
    {
        $credentials = $request->only('email','password');
        $user = User::where('email',$request->email)->first();
        $validator = Validator::make($request->all(),[
            'email'=> 'required|string',
            'password' => 'required|string|min:8'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 
            $response::HTTP_BAD_REQUEST);
        }

        if(!Auth::attempt($credentials)){
            if(!$user || !Hash::check($request['password'],$user->password))
            {
                return response()->json([
                    'status'=> $response::HTTP_UNAUTHORIZED,
                    'messafge'=>'Invalid credentials'
                ],$response::HTTP_UNAUTHORIZED);
            }
        }

        $token = $user->createToken('login_token')->plainTextToken;
        return response()->json([
            'status'=> $response::HTTP_OK,
            'messafge'=>'Login success',
            'data'=>  $request->user(),
            'access_token' => $token,
            'token_type' =>'Bearer'
        ],$response::HTTP_OK);
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Logout success'
        ],Response::HTTP_OK);
    }
}
