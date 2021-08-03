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
        // return response()->json(["result"=>$req->image], 200);
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        
        $rules = array(
            "title" => "required|min:3|unique:services",
            "image" => "required|mimes:jpg,png,jpeg|max:2048",
            "city" => "required|integer",
            "url" => "required|min:3|unique:services",
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
            $service->city = strip_tags($req->city);
            $service->url = strtolower(strip_tags($req->url));
            $service->user_id = $user->id;
            $result = $service->save();
            if($result){
                return response()->json(["result"=>"Department created"], 201);
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
            return response()->json(["error"=>"invalid department id"], 200);
        }

        

        $rules = array(
            "title" => "required|min:3",
            "city" => "required|integer",
            "url" => "required|min:3",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            $services = Service::all();
            foreach($services as $services){
                if($services->title == $req->title && $services->id!=$id){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('title', 'This title is taken');
                    return $validator->errors();
                }else if($services->url == strtolower(strip_tags($req->url)) && $services->id!=$id){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('url', 'This url is taken');
                    return $validator->errors();
                }
            }
            $service->title = strip_tags($req->title);
            $service->city = strip_tags($req->city);
            $service->url = strtolower(strip_tags($req->url));
            $result = $service->save();
            
            if($result){
                return response()->json(["result"=>"Department updated"], 201);
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
            return response()->json(["error"=>"invalid department id"], 200);
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
                return response()->json(["result"=>"Department updated"], 201);
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
            return response()->json(["error"=>"invalid department id"], 200);
        }

        unlink(public_path('service/logo/'.$service->logo));
        $service->delete();
        return response()->json(["result"=>"Department deleted"], 200);
    }

     //view services logo
     public function view(){
        $services = Service::join('cities', 'cities.id', '=', 'services.city')
        ->get(['services.*', 'cities.id as city_id', 'cities.name as city_name']);

        return response()->json(["result"=>$services], 200);
     }
     
     //view services by id
     public function viewById($service_id){
        $services = Service::join('cities', 'cities.id', '=', 'services.city')->where('services.id',$service_id)
        ->get(['services.*', 'cities.id as city_id', 'cities.name as city_name']);

        return response()->json(["result"=>$services], 200);
     }


}
