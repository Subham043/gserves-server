<?php

namespace App\Http\Controllers;
use App\Models\Contact_Enquiry;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ContactEnquiryFormController extends Controller
{
    //create contact Enquiry
    public function create(Request $req){

        $rules = array(
            "name" => "required|min:3",
            "email" => "required|unique:users|email",
            "phone" => "required|min:10|max:15",
            "message" => "required|min:3",
        );
        
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{
            $contact_enquiry = new Contact_Enquiry;
            $contact_enquiry->name = (strip_tags($req->name));
            $contact_enquiry->email = (strip_tags($req->email));
            $contact_enquiry->phone = (strip_tags($req->phone));
            $contact_enquiry->message = strip_tags($req->message);
            $result = $contact_enquiry->save();
            
            if($result){
                return response()->json(["result"=>"Enquiry Submitted", "id" => $contact_enquiry->id], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

    //view all Contact Enquiry
    public function viewAll(){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $form_field = Contact_Enquiry::all();
        return response()->json(["result"=>$form_field], 200);
    }

    //delete contact Enquiry
    public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Contact_Enquiry::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid contact enquiry form id"], 200);
        }

        $service->delete();
        return response()->json(["result"=>"Contact Enquiry Deleted"], 200);
    }

}
