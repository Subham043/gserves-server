<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    //create cities
    public function create(Request $req){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

       

        $rules = array(
            "name" => "required|min:3",
            "state" => "required|min:3",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{

            
            $city = new City;
            $city->name = strip_tags($req->name);
            $city->state = strip_tags($req->state);
            $city->user_id = $user->id;
            $result = $city->save();
           
            if($result){
                return response()->json(["result"=>"city created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

     //update city
     public function update(Request $req, $city_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $city = City::find($city_id);
        if(!$city){
            return response()->json(["error"=>"invalid city id"], 200);
        }

        $rules = array(
            "name" => "required|min:3",
            "state" => "required|min:3",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $sub_service->name = strip_tags($req->name);
            $sub_service->state = strip_tags($req->state);
            $result = $sub_service->save();
            
            if($result){
                return response()->json(["result"=>"city updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

    //delete city
    public function delete($city_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $city = City::find($city_id);
        if(!$city){
            return response()->json(["error"=>"invalid city id"], 200);
        }

        $city->delete();
        return response()->json(["result"=>"city deleted"], 200);
    }

    //view sub-services
    public function view(){
        return response()->json(["result"=>City::all()], 200);
    }

    //view sub-services by service id
    public function viewById($city_id){
        return response()->json(["result"=>City::where('id',$city_id)->get()], 200);
    }



}
