<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Sub_Service;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SubServiceController extends Controller
{
    //create sub-services
    public function create(Request $req, $service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

       

        $rules = array(
            "title" => "required|min:3",
            "storage_table_name" => "required|min:3|alpha|max:30|unique:sub__services",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{

            $serv = Service::find($service_id);
            if(!$serv){
                return response()->json(["error"=>"invalid service id"], 200);
            }
            
            $service = new Sub_Service;
            $service->name = strip_tags($req->name);
            $service->storage_table_name = strip_tags($req->storage_table_name);
            $service->service_id = $service_id;
            $service->user_id = $user->id;
            $result = $service->save();
            Schema::create(strip_tags($req->storage_table_name), function (Blueprint $table) {
                $table->id();
                $table->integer('sub_service_id');
                $table->integer('service_id');
                $table->integer('user_id');
                $table->timestamp('created_at')->nullable(true)->useCurrent();
                $table->timestamp('updated_at')->nullable(true)->useCurrent();
            });
            if($result){
                return response()->json(["result"=>"sub-service created"], 201);
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

        $sub_service = Sub_Service::find($id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $rules = array(
            "name" => "required|min:3",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $sub_service->name = strip_tags($req->name);
            $result = $sub_service->save();
            
            if($result){
                return response()->json(["result"=>"sub-service updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

    //delete sub-services
    public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        Schema::dropIfExists($sub_service->storage_table_name);
        $sub_service->delete();
        return response()->json(["result"=>"sub-service deleted"], 200);
    }

    //view sub-services
    public function view(){
        return response()->json(["result"=>Sub_Service::all()], 200);
    }
}
