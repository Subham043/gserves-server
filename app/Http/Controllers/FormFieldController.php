<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Sub_Service;
use App\Models\Sub_Service_Fields;
use App\Models\User;
use App\Models\Form_Field;
use App\Models\Choices;
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
            "field_name" => "required|min:3|max:30|unique:form__fields",
            "display_name" => "required|min:3|max:30",
            "field_type" => "required|min:3|max:30",
        );
        // if(!empty((strip_tags($req->dependent_field_id)))){
        //     $rules["operator"] = "required|min:1|max:3";
        //     $rules["operated_value"] = "required";
        // }
        // if(!empty((strip_tags($req->mandatory)))){
        //     $rules["mandatory"] = "required|integer";
        // }
        if(strip_tags($req->field_type)=="multiple choice"){
            $rules["choice"] = "required";
        }else if(strip_tags($req->field_type)=="attatchment"){
            $rules["length"] = "";
        }else{
            $rules["length"] = "required|integer";
        }

        // if(strip_tags($req->field_type)!="attatchment" && strip_tags($req->field_type)!="multiple choice" ){
        //     $rules["length"] = "required|integer";
        // }
        
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{
            // $Field_Name = strtolower(str_replace(' ', '_', strip_tags($req->field_name))).time();
            $form_field = new Form_Field;
            $form_field->field_name = (strip_tags($req->field_name));
            $form_field->display_name = (strip_tags($req->display_name));
            // $form_field->field_column_name = strtolower(str_replace(' ', '_', strip_tags($req->field_name))).time().random_int(1000, 9999);
            $form_field->field_type = strip_tags($req->field_type);
            // if(!empty((strip_tags($req->dependent_field_id)))){
            //     $form_field->dependent_field_id = (strip_tags($req->dependent_field_id));
            //     $form_field->operator = (strip_tags($req->operator));
            //     $form_field->operated_value = (strip_tags($req->operated_value));
            // }
            // if(!empty((strip_tags($req->mandatory)))){
            //     $form_field->mandatory = (strip_tags($req->mandatory));
            // }
            if(strip_tags($req->field_type)!="multiple choice" && strip_tags($req->field_type)!="attatchment"){
                $form_field->length = (strip_tags($req->length));
            }
            // return["test"=>$req->choice];
            // exit;
            $form_field->user_id = $user->id;
            $result = $form_field->save();
            
            if($result){
                if(!empty($req->choice) && strip_tags($req->field_type)=="multiple choice"){
                    foreach($req->choice as $choice){
                        $choice_table = new Choices;
                        $choice_table->choice = (strip_tags($choice));
                        $choice_table->user_id = $user->id;
                        $choice_table->form_field_id = $form_field->id;
                        $choice_table->save();
                    }
                    return response()->json(["result"=>"form field created", "id" => $form_field->id], 201);
                }else{
                    return response()->json(["result"=>"form field created", "id" => $form_field->id], 201);
                }
                
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

        $form__fields = DB::table('form__fields')->get()->toArray();
        $choices = DB::table('choices')->get()->toArray();
        
        foreach($form__fields as &$form__field)
        {
            $form__field->choices = array();
            foreach($choices as &$choice)
            {
                if($choice->form_field_id==$form__field->id){
                    array_push($form__field->choices, $choice);
                }
            }
            
        }

        return response()->json(["result"=>$form__fields], 200);
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

        $form__fields = DB::table('form__fields')->where('id', $id)->get()->toArray();
        $choices = DB::table('choices')->get()->toArray();

        foreach($form__fields as $form__field)
        {
            $form__field->choices = array();
            foreach($choices as &$choice)
            {
                if($choice->form_field_id==$form__field->id){
                    array_push($form__field->choices, $choice);
                }
            }
        }

        return response()->json(["result"=>$form__fields[0]], 200);
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

      //delete form-field
    public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $form_field = Form_Field::find($id);
        if(!$form_field){
            return response()->json(["error"=>"invalid form-field id"], 200);
        }

        $form_field->delete();
        return response()->json(["result"=>"form field deleted"], 200);
    }


}