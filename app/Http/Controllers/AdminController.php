<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
     //login
     function login(Request $req)
     {
        $rules = array(
            "email" => "required|email",
            "password" => "required|min:5",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            if(count(User::where('email', $req->email)->where('email_verified', '1')->where('is_admin', '1')->get())==0){
                return response()->json(["error"=>"Invalid Email"], 200);
            }else{
                $user= User::where('email', strip_tags($req->email))->where('email_verified', '1')->where('is_admin', '1')->first();
                if (!$user || !Hash::check(strip_tags($req->password), $user->password)) {
                   
                    return response()->json(["error"=>"These credentials do not match our records."], 200);
                }else {
                    $token = $user->createToken('my-app-token')->plainTextToken;
            
                    return response()->json(["result"=>$token], 201);
                }
            
                
            }
        }
             
     }

     //forgot-password
     function forgot_password(Request $req)
     {
        $rules = array(
            "email" => "required|email",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            if(count(User::where('email', $req->email)->where('email_verified', '1')->where('is_admin', '1')->get())==0){
                return response()->json(["error"=>"Invalid Email"], 200);
            }else{
                $user= User::where('email', strip_tags($req->email))->where('email_verified', '1')->where('is_admin', '1')->first();
                return response()->json(["verified_email"=>$req->email], 200);
            }
        }
     }

     //reset-password
     function reset_password(Request $req, $email)
     {
        $rules = array(
            "otp" => "required|min:6|max:6",
            "password" => "required|min:5",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            if(count(User::where('email', $email)->where('email_verified', '1')->where('is_admin', '1')->get())==0){
                return response()->json(["error"=>"Invalid Email"], 200);
            }else if(count(User::where('email', $email)->where('email_verified', '1')->where('is_admin', '1')->where('otp', strip_tags($req->otp))->get())==0){
                return response()->json(["error"=>"Invalid Otp"], 200);
            }{
                $user = User::where('email', $email)->where('email_verified', '1')->where('is_admin', '1')->where('otp', strip_tags($req->otp))->first();
                $user->password = Hash::make(strip_tags($req->password));
                $user->otp=random_int(100000, 999999);
                $result = $user->save();
                if($result){
                    return response()->json(["result"=>"Password Updated Successfully"], 200);
                }else{
                    return response()->json(["error"=>"something went wrong. Please try again"], 200);
                }
            }
        }
     }



}
