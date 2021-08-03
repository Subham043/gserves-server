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
            "name" => "required|min:3",
            "description" => "required",
            "tag_line" => "required",
            "city" => "required|integer",
            "output" => "required",
            "time_taken" => "required",
            "govt_fees" => "required|integer",
            "other_expenses" => "required|integer",
            "service_charges" => "required|integer",
            "tracking_url" => "required",
            "storage_table_name" => "required|min:3|alpha|max:30|unique:sub__services",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{

            $serv = Service::find($service_id);
            if(!$serv){
                return response()->json(["error"=>"invalid master service"], 200);
            }
            
            $service = new Sub_Service;
            $service->name = strip_tags($req->name);
            $service->description = strip_tags($req->description);
            $service->tag_line = strip_tags($req->tag_line);
            $service->city = strip_tags($req->city);
            $service->output = strip_tags($req->output);
            $service->option_online = strip_tags($req->option_online);
            $service->option_person = strip_tags($req->option_person);
            $service->option_representative = strip_tags($req->option_representative);
            $service->time_taken = strip_tags($req->time_taken);
            $service->govt_fees = strip_tags($req->govt_fees);
            $service->other_expenses = strip_tags($req->other_expenses);
            $service->service_charges = strip_tags($req->service_charges);
            $service->tracking_url = strip_tags($req->tracking_url);
            $service->storage_table_name = strtolower(strip_tags($req->storage_table_name));
            $service->service_id = $service_id;
            $service->user_id = $user->id;
            $result = $service->save();
            Schema::create(strtolower(strip_tags($req->storage_table_name)), function (Blueprint $table) {
                $table->id();
                $table->integer('sub_service_id');
                $table->integer('service_id');
                $table->integer('user_id');
                $table->timestamp('created_at')->nullable(true)->useCurrent();
                $table->timestamp('updated_at')->nullable(true)->useCurrent();
            });
            if($result){
                return response()->json(["result"=>"Master Service created"], 201);
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
            return response()->json(["error"=>"invalid Master Service id"], 200);
        }

        $rules = array(
            "name" => "required|min:3",
            "description" => "required",
            "tag_line" => "required",
            "city" => "required|integer",
            "output" => "required",
            "time_taken" => "required",
            "govt_fees" => "required|integer",
            "other_expenses" => "required|integer",
            "service_charges" => "required|integer",
            "tracking_url" => "required",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $sub_service->name = strip_tags($req->name);
            $sub_service->description = strip_tags($req->description);
            $sub_service->tag_line = strip_tags($req->tag_line);
            $sub_service->city = strip_tags($req->city);
            $sub_service->output = strip_tags($req->output);
            $sub_service->option_online = strip_tags($req->option_online);
            $sub_service->option_person = strip_tags($req->option_person);
            $sub_service->option_representative = strip_tags($req->option_representative);
            $sub_service->time_taken = strip_tags($req->time_taken);
            $sub_service->govt_fees = strip_tags($req->govt_fees);
            $sub_service->other_expenses = strip_tags($req->other_expenses);
            $sub_service->service_charges = strip_tags($req->service_charges);
            $sub_service->tracking_url = strip_tags($req->tracking_url);
            $result = $sub_service->save();
            
            if($result){
                return response()->json(["result"=>"Master Service updated"], 201);
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
            return response()->json(["error"=>"invalid Master Service id"], 200);
        }

        Schema::dropIfExists($sub_service->storage_table_name);
        $sub_service->delete();
        return response()->json(["result"=>"Master Service deleted"], 200);
    }

    //view sub-services
    public function view(){
        $services = Sub_Service::join('cities', 'cities.id', '=', 'sub__services.city')
        ->join('services', 'services.id', '=', 'sub__services.service_id')
        ->get(['sub__services.*', 'cities.id as city_id', 'cities.name as city_name', 'services.title as service_name']);

        return response()->json(["result"=>$services], 200);
    }

    //view sub-services by service id
    public function viewById($service_id){
        $services = Sub_Service::where('service_id',$service_id)->join('cities', 'cities.id', '=', 'sub__services.city')
        ->join('services', 'services.id', '=', 'sub__services.service_id')
        ->get(['sub__services.*', 'cities.id as city_id', 'cities.name as city_name', 'services.title as service_name']);

        return response()->json(["result"=>$services], 200);
    }

    //view sub-services by id
    public function viewBySubServiceId($sub_service_id){
        $services = Sub_Service::where('id',$sub_service_id)
        ->get();

        return response()->json(["result"=>$services], 200);
    }

}
