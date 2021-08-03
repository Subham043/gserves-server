<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Sub_Service;
use App\Models\Sub_Service_Fields;
use App\Models\User;
use App\Models\Form_Field;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FormFieldController extends Controller
{
    //create form-fields
    public function create(Request $req){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

       

        $rules = array(
            "field_name" => "required|min:3|max:30",
            "field_type" => "required|min:3|alpha|max:30",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{

            $Field_Name = strtolower(str_replace(' ', '_', strip_tags($req->field_name))).time();
            $form_field = new Form_Field;
            $form_field->field_name = (strip_tags($req->field_name));
            $form_field->field_column_name = strtolower(str_replace(' ', '_', strip_tags($req->field_name))).time().random_int(1000, 9999);
            $form_field->field_type = strip_tags($req->field_type);
            $form_field->user_id = $user->id;
            $result = $form_field->save();
            
            if($result){
                return response()->json(["result"=>"form field created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

    //view all form-fields
    public function viewAll(){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $form_field = Form_Field::all();
        return response()->json(["result"=>$form_field], 200);
    }

    //view all form-fields by id
    public function view($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $form_field = Form_Field::find($id);
        return response()->json(["result"=>$form_field], 200);
    }

    //view set status to 0 or 1 for unrequired fields
    public function set_status($id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"admin unauthorised"], 200);
        }

        $form_field = Form_Field::find($id);
        if(!$form_field){
            return response()->json(["error"=>"invalid form-field id"], 200);
        }

        if($form_field->status == 1){
            $form_field->status = 0;
            $result = $form_field->save();
            if($result){
                return response()->json(["result"=>$form_field->field_name." field is inactive"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }else{
            $form_field->status = 1;
            $result = $form_field->save();
            if($result){
                return response()->json(["result"=>$form_field->field_name." field is active"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

     }


}
