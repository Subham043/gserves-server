<?php

namespace App\Http\Controllers;
use App\Models\Requirement_Enquiry;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class RequirementEnquiryFormController extends Controller
{
    //create Requirement Enquiry
    public function create(Request $req){

        $rules = array(
            "name" => "required|min:3",
            "email" => "required|unique:users|email",
            "phone" => "required|min:10|max:15",
            "requirement_time" => "required|min:3",
            "service" => "required|min:3",
        );
        
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{
            $requirement_enquiry = new Requirement_Enquiry;
            $requirement_enquiry->name = (strip_tags($req->name));
            $requirement_enquiry->email = (strip_tags($req->email));
            $requirement_enquiry->phone = (strip_tags($req->phone));
            $requirement_enquiry->service = strip_tags($req->service);
            $requirement_enquiry->requirement_time = strip_tags($req->requirement_time);
            $result = $requirement_enquiry->save();
            
            if($result){
                return response()->json(["result"=>"Enquiry Submitted", "id" => $requirement_enquiry->id], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

    //view all Requirement Enquiry
    public function viewAll(){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $form_field = Requirement_Enquiry::all();
        return response()->json(["result"=>$form_field], 200);
    }

    //delete Requirement Enquiry
    public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Requirement_Enquiry::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid requirement enquiry form id"], 200);
        }

        $service->delete();
        return response()->json(["result"=>"Requirement Enquiry Deleted"], 200);
    }


}
