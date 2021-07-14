<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Sub_Service;
use App\Models\Sub_Service_Fields;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class SubServiceFieldController extends Controller
{
    //create sub-services-fields
    public function create(Request $req, $sub_service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

       

        $rules = array(
            "field_name" => "required|min:3|alpha|max:30|unique:sub__service__fields",
            "field_type" => "required|min:3|alpha|max:30",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{

            $sub_service = Sub_Service::find($sub_service_id);
            if(!$sub_service){
                return response()->json(["error"=>"invalid sub-service id"], 200);
            }
            $Field_Name = strip_tags($req->field_name);
            $service_field = new Sub_Service_Fields;
            $service_field->field_name = strip_tags($req->field_name);
            $service_field->field_type = strip_tags($req->field_type);
            $service_field->sub_service_id = $sub_service_id;
            $service_field->service_id = $sub_service->service_id;
            $service_field->user_id = $user->id;
            $result = $service_field->save();

            if($req->field_type=="email" || $req->field_type=="text"){
                Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                    $table->string($Field_Name)->nullable(true);
                });
            }else if($req->field_type=="description" ){
                Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                    $table->text($Field_Name)->nullable(true);
                });
            }
            else if($req->field_type=="number" || $req->field_type=="mobile" ){
                Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                    $table->integer($Field_Name)->nullable(true);
                });
            }else if($req->field_type=="date" ){
                Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                    $table->date($Field_Name)->nullable(true);
                });
            }
            
            if($result){
                return response()->json(["result"=>"sub-service-fields created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

     //view custom-sub-services-fields using sub-services id
     public function view($sub_service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        

        $sub_service_fields = Sub_Service_Fields::join('sub__services', 'sub__service__fields.sub_service_id', '=', 'sub__services.id')->where("sub__services.id", $sub_service_id)->where("sub__service__fields.status", 1)
        ->get(['sub__service__fields.field_name', 'sub__service__fields.field_type', 'sub__service__fields.status', 'sub__service__fields.sub_service_id', 'sub__service__fields.service_id', 'sub__services.storage_table_name', 'sub__services.title']);

        return response()->json(["result"=>$sub_service_fields], 200);

     }

     //view set status to 0 or 1 for unrequired fields
     public function set_status($sub_service_fields_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service_fields = Sub_Service_Fields::find($sub_service_fields_id);
        if(!$sub_service_fields){
            return response()->json(["error"=>"invalid sub-service-field id"], 200);
        }

        if($sub_service_fields->status == 1){
            $sub_service_fields->status = 0;
            $result = $sub_service_fields->save();
            if($result){
                return response()->json(["result"=>$sub_service_fields->field_name." field is unset"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }else{
            $sub_service_fields->status = 1;
            $result = $sub_service_fields->save();
            if($result){
                return response()->json(["result"=>$sub_service_fields->field_name." field is set"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

     }

     //create sub-services-fields entry
    public function create_custom_sub_service_field_data(Request $req, $sub_service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        

        $sub_service_fields = Sub_Service_Fields::join('sub__services', 'sub__service__fields.sub_service_id', '=', 'sub__services.id')->where("sub__services.id", $sub_service_id)->where("sub__service__fields.status", 1)
        ->get(['sub__service__fields.field_name', 'sub__service__fields.field_type', 'sub__service__fields.status', 'sub__service__fields.sub_service_id', 'sub__service__fields.service_id', 'sub__services.storage_table_name', 'sub__services.title']);

        $rules = array();

        foreach ($sub_service_fields as $obj) {
            if($obj->field_type=="email"){
                $rules[$obj->field_name] =  "required|min:3|email";
            }else if($obj->field_type=="text"){
                $rules[$obj->field_name] =  "required|min:3";
            }else if($obj->field_type=="description" || $obj->field_type=="date"){
                $rules[$obj->field_name] =  "required";
            }else if($obj->field_type=="number" || $obj->field_type=="mobile"){
                $rules[$obj->field_name] =  "required|integer";
            }
            
         }

         $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{

            $data = array();
            foreach ($sub_service_fields as $obj) {
                if(($obj->field_name!="sub_service_id") && ($obj->field_name!="service_id") && ($obj->field_name!="user_id")){
                    $data[$obj->field_name] =  strip_tags($req->input($obj->field_name));
                }
                
            }
            $data["sub_service_id"] =  $sub_service_id;
            $data["service_id"] =  $sub_service_fields[0]->service_id;
            $data["user_id"] =  $user->id;
            $Table = $sub_service_fields[0]->storage_table_name;

            try {
                DB::transaction(function () use ($data , $Table) {
                    DB::table($Table)->insert($data);
                });
                return response()->json(["result"=>"data stored"], 201);
            }
            catch(Exception $e) {
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
            

        }
        
       

    }

       
    


}
