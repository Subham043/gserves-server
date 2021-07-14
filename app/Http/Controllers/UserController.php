<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    //create users
    public function create(Request $req){

        $rules = array(
            "first_name" => "required|min:3",
            "last_name" => "required|min:3",
            "email" => "required|unique:users|email",
            "phone" => "required|min:10|max:15",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            if(count(User::where('email', $req->email)->get())==1){
                return response()->json(["error"=>"Email already exists"], 200);
            }
            else{
                $user = new User;
                $user->first_name = strip_tags($req->first_name);
                $user->last_name = strip_tags($req->last_name);
                $user->email = strip_tags($req->email);
                $user->phone = strip_tags($req->phone);
                $user->otp = random_int(100000, 999999);
                $result = $user->save();
                if($result){
                    return response()->json(["result"=>"user created", "email" => $user->email], 201);
                }else{
                    return response()->json(["error"=>"something went wrong. Please try again"], 200);
                }
                
            }
        }
    }

    

    //login
    function login(Request $request)
    {
            $user= User::where('email', strip_tags($request->email))->first();
            if (!$user || !Hash::check(strip_tags($request->password), $user->password)) {
               
                return response()->json(["error"=>"These credentials do not match our records."], 200);
            }else if($user->email_verified == 0){
                return response()->json(["error"=>"Please verify your phone number."], 200);
            }
        
            $token = $user->createToken('my-app-token')->plainTextToken;
        
            return response()->json(["result"=>$token], 201);
    }

    //logout
    function logout()
    {
        $user = auth()->user();
        if($user){
            $user->social_id = null;
            $result = $user->save();
            $user->tokens()->delete();
            return ["result" => "successfully logged out"];
        }else{
            return ["error" => "invalid token"];
        }
    }

     //verify users
     public function verify(Request $req, $email){

        if(count(User::where('email', $email)->get())==0){
            return response()->json(["error"=>"Email Doesnot exist"], 200);
        }else{
            $user = User::where('email', $email)->get()->first();
        }

        $rules = array(
            "otp" => "required|min:6|max:6",
            "password" => "required|min:5",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            if($user->otp==$req->otp){
                $user->password = Hash::make(strip_tags($req->password));
                $user->email_verified = 1;
                $user->email_verified_at = now();
                $user->otp=random_int(100000, 999999);
                $result = $user->save();
                if($result){
                    return response()->json(["result"=>"User Otp verified and User Verified"], 200);
                }else{
                    return response()->json(["error"=>"something went wrong. Please try again"], 200);
                }
            }else{
                return response()->json(["error"=>"Invalid Otp"], 200);
            }
           
        }
    }

     //log in with google
     public function google(Request $req){
        
            if(count(User::where('email', $req->email)->where('email_verified',1)->get())==1){
                
                $user = User::where('email', $req->email)->where('email_verified',1)->get()->first();
                $user->social_id = strip_tags($req->social_id);
                $result = $user->save();
                $token = $user->createToken('my-app-token')->plainTextToken;
                return response()->json(["result"=>"Email verified", "token" => $token], 200);
            }else if(count(User::where('email', $req->email)->where('email_verified',0)->get())==1){
                return response()->json(["result"=>"Email not verified", "email" => $req->email], 200);
            }
            else{
                $user = new User;
                $user->first_name = strip_tags($req->first_name);
                $user->last_name = strip_tags($req->last_name);
                $user->email = strip_tags($req->email);
                $user->social_id = strip_tags($req->social_id);
                $user->otp = random_int(100000, 999999);
                $result = $user->save();
                if($result){
                    return response()->json(["result"=>"user created", "email" => $user->email], 201);
                }else{
                    return response()->json(["error"=>"something went wrong. Please try again"], 200);
                }
                
            }
        
     }

     //log in with google
     public function google_phone(Request $req, $email){

        if(count(User::where('email', $email)->get())==1){
            $user = User::where('email', $email)->get()->first();
            if($user->social_id == null){
                return response()->json(["result"=>"illegal email"], 200);
            }else if($user->email_verified == 1){
                return response()->json(["result"=>"illegal email"], 200);
            }
            
        }else{
            return response()->json(["result"=>"illegal email"], 200);
        }

        if(count(User::where('email', $email)->where('email_verified',1)->get())==1){
            return response()->json(["result"=>"Email verified"], 200);
        }else if(count(User::where('email', $email)->where('email_verified',0)->get())==1){
            $rules = array(
                "phone" => "required|min:10|max:15",
            );
            $validator = Validator::make($req->all(), $rules);
            if($validator->fails()){
                return $validator->errors();
            }else{
                    $user = User::where('email', $email)->get()->first();
                    $user->phone = strip_tags($req->phone);
                    $result = $user->save();
                    if($result){
                        return response()->json(["result"=>"User phone number saved", "email" => $email], 200);
                    }else{
                        return response()->json(["error"=>"something went wrong. Please try again"], 200);
                    }
               
               
            }
        }else{
            return response()->json(["error"=>"invalid email"], 200);
        }
        
        
    
 }

 public function google_verify(Request $req, $email){

    if(count(User::where('email', $email)->get())==1){
        $user = User::where('email', $email)->get()->first();
        if($user->social_id == null){
            return response()->json(["result"=>"illegal email"], 200);
        }else if($user->email_verified == 1){
            return response()->json(["result"=>"illegal email"], 200);
        }
        
    }else{
        return response()->json(["result"=>"illegal email"], 200);
    }
    if(count(User::where('email', $email)->get())==0){
        return response()->json(["error"=>"Email Doesnot exist"], 200);
    }else{
        $user = User::where('email', $email)->get()->first();
    }

    $rules = array(
        "otp" => "required|min:6|max:6",
        "password" => "required|min:5",
    );
    $validator = Validator::make($req->all(), $rules);
    if($validator->fails()){
        return $validator->errors();
    }else{
        if($user->otp==$req->otp){
            $user->password = Hash::make(strip_tags($req->password));
            $user->email_verified = 1;
            $user->email_verified_at = now();
            $user->otp=random_int(100000, 999999);
            $result = $user->save();
            if($result){
                $token = $user->createToken('my-app-token')->plainTextToken;
                return response()->json(["result"=>"User Otp verified and User Verified", "token" => $token], 200);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }else{
            return response()->json(["error"=>"Invalid Otp"], 200);
        }
       
    }
}

}