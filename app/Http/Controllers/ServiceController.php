<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    
    //create services
    public function create(Request $req){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $rules = array(
            "title" => "required|min:3",
            "image" => "required|mimes:jpg,png,jpeg|max:2048",
            "price" => "required|integer",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else if(!$req->file('image')->isValid()){
            return response()->json(["error"=>"invalid image"], 200);
        }else{
            $newImage = time().'-'.$req->image->getClientOriginalName();
            $req->image->move(public_path('service/logo'), $newImage);
            $service = new Service;
            $service->title = strip_tags($req->title);
            $service->logo = $newImage;
            $service->price = strip_tags($req->price);
            $service->user_id = $user->id;
            $result = $service->save();
            if($result){
                return response()->json(["result"=>"service created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

    //update services
    public function update(Request $req, $id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Service::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid service id"], 200);
        }

        $rules = array(
            "title" => "required|min:3",
            "price" => "required|integer",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $service->title = strip_tags($req->title);
            $service->price = strip_tags($req->price);
            $result = $service->save();
            
            if($result){
                return response()->json(["result"=>"service updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

     //update services logo
     public function update_logo(Request $req, $id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Service::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid service id"], 200);
        }

        $rules = array(
            "image" => "required|mimes:jpg,png,jpeg|max:2048",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            unlink(public_path('service/logo/'.$service->logo));
            $newImage = time().'-'.$req->image->getClientOriginalName();
            $req->image->move(public_path('service/logo'), $newImage);
            $service->logo = $newImage;
            $result = $service->save();
            
            if($result){
                return response()->json(["result"=>"service updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

    //delete services logo
    public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Service::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid service id"], 200);
        }

        unlink(public_path('service/logo/'.$service->logo));
        $service->delete();
        return response()->json(["result"=>"service deleted"], 200);
    }

     //delete services logo
     public function view(){
        return response()->json(["result"=>Service::all()], 200);
        // $sub_service_fields = DB::table('services')
        // ->join('sub__services', 'sub__services.service_id', '=', 'services.id')
        // ->select('sub__services.id', 'sub__services.name', 'services.title', 'services.logo', 'services.price',)
        // ->get();
        // return response()->json(["result"=>$sub_service_fields], 200);

     }


}
