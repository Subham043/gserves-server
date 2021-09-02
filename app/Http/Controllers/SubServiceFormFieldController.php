<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Sub_Service;
use App\Models\Form_Field;
use App\Models\Choices;
use App\Models\User;
use App\Models\Sub_Service_Form_Field;
use App\Models\SubServiceFormFieldChoice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SubServiceFormFieldController extends Controller
{
    //create sub service form fields
    public function create(Request $req, $sub_service_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service->id)->get();


        if(!empty($req->fields)){
            if(count($sub_service_form_field)==0){
                foreach($req->fields as $fields){

                    if(empty(strip_tags($fields['dependent_field_name']))){
                        if(!empty(strip_tags($fields['operator']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operated_value']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if(empty(strip_tags($fields['operator']))){
                        if(!empty(strip_tags($fields['dependent_field_name']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operated_value']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if(empty(strip_tags($fields['operated_value']))){
                        if(!empty(strip_tags($fields['dependent_field_name']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operator']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }

                    if((strip_tags($fields['dependent_field_name']))==null){
                        if((strip_tags($fields['operator']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operated_value']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if((strip_tags($fields['operator']))==null){
                        if((strip_tags($fields['dependent_field_name']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operated_value']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if((strip_tags($fields['operated_value']))==null){
                        if((strip_tags($fields['dependent_field_name']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operator']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }


                    $sub_service_form_field = new Sub_Service_Form_Field;
                    $Field_Name = (strip_tags($fields['display_name'])).'_'.(strip_tags($fields['field_name'])).'_'.time();
                    $sub_service_form_field->field_name = (strip_tags($fields['field_name']));
                    $sub_service_form_field->display_name = (strip_tags($fields['display_name']));
                    $sub_service_form_field->field_column_name = $Field_Name;
                    $sub_service_form_field->field_type = (strip_tags($fields['field_type']));
                    if((!empty(strip_tags($fields['length']))) || strip_tags($fields['length'])!=null){
                        $sub_service_form_field->length = (strip_tags($fields['length']));
                    }
                    if((!empty(strip_tags($fields['dependent_field_name']))) || strip_tags($fields['dependent_field_name'])!=null){
                        $sub_service_form_field->dependent_field_name = (strip_tags($fields['dependent_field_name']));
                        $sub_service_form_field->operator = (strip_tags($fields['operator']));
                        $sub_service_form_field->operated_value = (strip_tags($fields['operated_value']));
                    }
                    $sub_service_form_field->mandatory = (strip_tags($fields['mandatory']));
                    $sub_service_form_field->status = (strip_tags($fields['status']));
                    $sub_service_form_field->order_number = (strip_tags($fields['order_number']));
                    $sub_service_form_field->storage_table_name = $sub_service->storage_table_name;
                    $sub_service_form_field->sub_service_id = $sub_service->id;
                    $sub_service_form_field->service_id = $sub_service->service_id;
                    $sub_service_form_field->user_id = $user->id;
                    $sub_service_form_field->save();

                    if($sub_service_form_field->field_type=="multiple choice"){
                        $form_field_for_choice = Form_Field::where('field_name',$sub_service_form_field->field_name)->where('field_type',"multiple choice")->get();
                        $choice = DB::table('choices')->where('form_field_id',$form_field_for_choice->id)->get()->toArray();
                        foreach($choice as $choice){
                            $choice_table = new SubServiceFormFieldChoice;
                            $choice_table->choice = $choice->choice;
                            $choice_table->sub_service_form_field_id = $sub_service_form_field->id;
                            $choice_table->save();
                        }
                    }

                    if($sub_service_form_field->field_type=="email" || $sub_service_form_field->field_type=="text"){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->string($Field_Name)->nullable(true);
                        });
                    }else if($sub_service_form_field->field_type=="description" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->text($Field_Name)->nullable(true);
                        });
                    }
                    else if($sub_service_form_field->field_type=="number" || $sub_service_form_field->field_type=="mobile" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->integer($Field_Name)->nullable(true);
                        });
                    }else if($sub_service_form_field->field_type=="date" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->date($Field_Name)->nullable(true);
                        });
                    }else if($sub_service_form_field->field_type=="attatchment" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->string($Field_Name)->nullable(true);
                        });
                    }
                }
                return ["result"=>"Fields Added Successfully","FormFieldList"=>Sub_Service_Form_Field::where('sub_service_id',$sub_service->id)->get()];
            }else{
                $exiting_field = array();
                $fields_array = $req->fields;
                foreach($fields_array as $fields=>$val){
                    for($j=0;$j<count($sub_service_form_field);$j++){
                        if($sub_service_form_field[$j]->field_name==$val['field_name']){
                            array_push($exiting_field,$val);
                            unset($fields_array[$fields]);
                        }
                    }
                }

                // new data
                foreach($fields_array as $fields){

                    if(empty(strip_tags($fields['dependent_field_name']))){
                        if(!empty(strip_tags($fields['operator']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operated_value']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if(empty(strip_tags($fields['operator']))){
                        if(!empty(strip_tags($fields['dependent_field_name']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operated_value']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if(empty(strip_tags($fields['operated_value']))){
                        if(!empty(strip_tags($fields['dependent_field_name']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operator']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }

                    if((strip_tags($fields['dependent_field_name']))==null){
                        if((strip_tags($fields['operator']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operated_value']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if((strip_tags($fields['operator']))==null){
                        if((strip_tags($fields['dependent_field_name']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operated_value']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if((strip_tags($fields['operated_value']))==null){
                        if((strip_tags($fields['dependent_field_name']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operator']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }

                    $sub_service_form_field = new Sub_Service_Form_Field;
                    $Field_Name = (strip_tags($fields['display_name'])).'_'.(strip_tags($fields['field_name'])).'_'.time();
                    $sub_service_form_field->field_name = (strip_tags($fields['field_name']));
                    $sub_service_form_field->display_name = (strip_tags($fields['display_name']));
                    $sub_service_form_field->field_column_name = $Field_Name;
                    $sub_service_form_field->field_type = (strip_tags($fields['field_type']));
                    if((!empty(strip_tags($fields['length']))) || strip_tags($fields['length'])!=null){
                        $sub_service_form_field->length = (strip_tags($fields['length']));
                    }
                    if((!empty(strip_tags($fields['dependent_field_name']))) || strip_tags($fields['dependent_field_name'])!=null){
                        $sub_service_form_field->dependent_field_name = (strip_tags($fields['dependent_field_name']));
                        $sub_service_form_field->operator = (strip_tags($fields['operator']));
                        $sub_service_form_field->operated_value = (strip_tags($fields['operated_value']));
                    }
                    $sub_service_form_field->mandatory = (strip_tags($fields['mandatory']));
                    $sub_service_form_field->status = (strip_tags($fields['status']));
                    $sub_service_form_field->order_number = (strip_tags($fields['order_number']));
                    $sub_service_form_field->storage_table_name = $sub_service->storage_table_name;
                    $sub_service_form_field->sub_service_id = $sub_service->id;
                    $sub_service_form_field->service_id = $sub_service->service_id;
                    $sub_service_form_field->user_id = $user->id;
                    $sub_service_form_field->save();

                    if($sub_service_form_field->field_type=="multiple choice"){
                        $form_field_for_choice = Form_Field::where('field_name',$sub_service_form_field->field_name)->where('field_type',"multiple choice")->get();
                        $choice = DB::table('choices')->where('form_field_id',$form_field_for_choice[0]->id)->get()->toArray();
                        foreach($choice as $choice){
                            $choice_table = new SubServiceFormFieldChoice;
                            $choice_table->choice = $choice->choice;
                            $choice_table->sub_service_form_field_id = $sub_service_form_field->id;
                            $choice_table->save();
                        }
                    }

                    if($sub_service_form_field->field_type=="email" || $sub_service_form_field->field_type=="text"){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->string($Field_Name)->nullable(true);
                        });
                    }else if($sub_service_form_field->field_type=="description" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->text($Field_Name)->nullable(true);
                        });
                    }
                    else if($sub_service_form_field->field_type=="number" || $sub_service_form_field->field_type=="mobile" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->integer($Field_Name)->nullable(true);
                        });
                    }else if($sub_service_form_field->field_type=="date" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->date($Field_Name)->nullable(true);
                        });
                    }else if($sub_service_form_field->field_type=="attatchment" ){
                        Schema::table($sub_service->storage_table_name, function (Blueprint $table) use ($Field_Name) {
                            $table->string($Field_Name)->nullable(true);
                        });
                    }
                }

                //old data
                foreach($exiting_field as $fields){

                    if(empty(strip_tags($fields['dependent_field_name']))){
                        if(!empty(strip_tags($fields['operator']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operated_value']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if(empty(strip_tags($fields['operator']))){
                        if(!empty(strip_tags($fields['dependent_field_name']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operated_value']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if(empty(strip_tags($fields['operated_value']))){
                        if(!empty(strip_tags($fields['dependent_field_name']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if(!empty(strip_tags($fields['operator']))){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }

                    if((strip_tags($fields['dependent_field_name']))==null){
                        if((strip_tags($fields['operator']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operated_value']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if((strip_tags($fields['operator']))==null){
                        if((strip_tags($fields['dependent_field_name']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operated_value']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }else if((strip_tags($fields['operated_value']))==null){
                        if((strip_tags($fields['dependent_field_name']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }else if((strip_tags($fields['operator']))!=null){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'Please check dependency field, operator, value');
                            return $validator->errors();
                        }
                    }

                    $sub_service_form_field = Sub_Service_Form_Field::where('field_name',(strip_tags($fields['field_name'])))->where('sub_service_id',$sub_service->id)->get();
                    if(!empty(strip_tags($fields['dependent_field_name']))){
                        $sub_service_form_field[0]->dependent_field_name = (strip_tags($fields['dependent_field_name']));
                        $sub_service_form_field[0]->operator = (strip_tags($fields['operator']));
                        $sub_service_form_field[0]->operated_value = (strip_tags($fields['operated_value']));
                    }
                    $sub_service_form_field[0]->mandatory = (strip_tags($fields['mandatory']));
                    $sub_service_form_field[0]->status = (strip_tags($fields['status']));
                    $sub_service_form_field[0]->order_number = (strip_tags($fields['order_number']));
                    $sub_service_form_field[0]->user_id = $user->id;
                    $sub_service_form_field[0]->save();
                }

                return ["result"=>"Fields Updated Successfully","FormFieldList"=>Sub_Service_Form_Field::where('sub_service_id',$sub_service->id)->get()];
            }

        }else{
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('error', 'Atleast one master field must be selected');
            return $validator->errors();
        }

    }

    //view all sub service form fields
    public function view_all($sub_service_id){
        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }
        return ["result"=>Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->get()];
    }

    //view all sub service form fields for search
    public function view_all_search($sub_service_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->get()->toArray();
        
        if(count($sub_service_form_field)==0){
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
        }else{
            $form__fields = DB::table('form__fields')->get()->toArray();
            
            foreach($form__fields as $form__field=>$val)
            {
                foreach($sub_service_form_field as $sub_service_form_fields)
                {
                    if($sub_service_form_fields['field_name']==$val->field_name){
                        unset($form__fields[$form__field]);
                    }
                }
                
            }

            $form_field_new_array = array();
            foreach($form__fields as $form__field)
            {
                array_push($form_field_new_array, $form__field);
            }
            
            $choices = DB::table('choices')->get()->toArray();
            foreach($form_field_new_array as &$form__field)
            {
                $form__field->choices = array();
                foreach($choices as &$choice)
                {
                    if($choice->form_field_id==$form__field->id){
                        array_push($form__field->choices, $choice);
                    }
                }
                
            }
            return response()->json(["result"=>$form_field_new_array], 200);
        }

    }

    //view all sub service form fields order wise
    public function view_all_order($sub_service_id){
        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub__service__form__fields = DB::table('sub__service__form__fields')->where('sub_service_id',$sub_service_id)->orderBy('order_number')->get()->toArray();
        $sub_service_form_field_choices = DB::table('sub_service_form_field_choices')->get()->toArray();
        
        foreach($sub__service__form__fields as &$form__field)
        {
            $form__field->choices = array();
            foreach($sub_service_form_field_choices as &$choice)
            {
                if($form__field->field_type=="multiple choice"){
                    if($choice->sub_service_form_field_id==$form__field->id){
                        array_push($form__field->choices, $choice);
                    }
                }
            }
            
        }
        
        return ["result"=>$sub__service__form__fields];
    }

    //create sub service form fields entry of data by user
    public function create_custom_sub_service_form_field_data_entry(Request $req, $sub_service_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->where('status',1)->orderBy('order_number')->get();
        if(count($sub_service_form_field)==0){
            return response()->json(["error"=>"no fields available for this form"], 200);
        }

        // return $sub_service_form_field[0]->storage_table_name;
        $data = array();

        foreach ($sub_service_form_field as $obj) {
            if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && ($obj->field_type=="attatchment")){
                if($obj->mandatory==1){
                    if(isset(($_FILES[$obj->field_column_name]))){
                        $extensions= array("jpeg","jpg","png");
                        $file_ext=strip_tags($req->file($obj->field_column_name)->extension());
                        if(in_array($file_ext,$extensions)=== false){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' supports .jpg, .png and .jpeg file');
                            return $validator->errors();
                        }else{
                            $newImage = time().'-'.strip_tags($req->file($obj->field_column_name)->getClientOriginalName());
                            $req->file($obj->field_column_name)->move(public_path('service/form'), $newImage);
                            $data[$obj->field_column_name] =  $newImage;
                        }
                    }else{
                        $validator = Validator::make([], []); // Empty data and rules fields
                        $validator->errors()->add('error', $obj->display_name.' requires a file to be uploaded');
                        return $validator->errors();
                    }
                    
                }else{
                    if(isset(($_FILES[$obj->field_column_name]))){
                        $extensions= array("jpeg","jpg","png");
                        $file_ext=strip_tags($req->file($obj->field_column_name)->extension());
                        if(in_array($file_ext,$extensions)=== false){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' supports .jpg, .png and .jpeg file');
                            return $validator->errors();
                        }else{
                            $newImage = time().'-'.strip_tags($req->file($obj->field_column_name)->getClientOriginalName());
                            $req->file($obj->field_column_name)->move(public_path('service/form'), $newImage);
                            $data[$obj->field_column_name] =  $newImage;
                        }
                    }else{
                        $data[$obj->field_column_name] =  null;
                    }
                }
               
            }
            else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="email")||($obj->field_type=="profile email"))){
                if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", strip_tags($req->input($obj->field_column_name))) ){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', 'invalid '.$obj->display_name.' format' );
                    return $validator->errors();
                }else{
                    if(!empty(strip_tags($req->input($obj->field_column_name)))){
                        $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                    }else{
                        $data[$obj->field_column_name] =  null;
                    }
                }
                
            }
            else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="mobile")||($obj->field_type=="profile mobile")||($obj->field_type=="number")||($obj->field_type=="profile whatsapp"))){
                if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^[0-9][0-9 ]*$/", strip_tags($req->input($obj->field_column_name))) ){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' can contain only numeric characters' );
                    return $validator->errors();
                }else{
                    if(!empty(strip_tags($req->input($obj->field_column_name)))){
                        $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                    }else{
                        $data[$obj->field_column_name] =  null;
                    }
                    
                }
                
            }
            else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="profile name")||($obj->field_type=="text"))){
                if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^[a-zA-Z][a-zA-Z ]*$/", strip_tags($req->input($obj->field_column_name))) ){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' can contain only letters and spaces' );
                    return $validator->errors();
                }else{
                    if(!empty(strip_tags($req->input($obj->field_column_name)))){
                        $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                    }else{
                        $data[$obj->field_column_name] =  null;
                    }
                    
                }
                
            }
            else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="description")||($obj->field_type=="date"))){
                if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                    return $validator->errors();
                }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^[a-z 0-9~%.:_\@\-\/\&+=,]+$/i", strip_tags($req->input($obj->field_column_name))) ){
                    $validator = Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('error', $obj->display_name.' cannot contain special characters' );
                    return $validator->errors();
                }else{
                    if(!empty(strip_tags($req->input($obj->field_column_name)))){
                        $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                    }else{
                        $data[$obj->field_column_name] =  null;
                    }
                    
                }
                
            }
            
            
        }

        $data["sub_service_id"] =  $sub_service_id;
        $data["service_id"] =  $sub_service_form_field[0]->service_id;
        $data["user_id"] =  $user->id;
        $Table = $sub_service_form_field[0]->storage_table_name;

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

    //view all sub service form fields entry of data by user for that particular form
    public function view_all_custom_sub_service_form_field_data_entry($sub_service_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->where('status',1)->orderBy('order_number')->get();
        if(count($sub_service_form_field)==0){
            return response()->json(["error"=>"no fields available for this form"], 200);
        }

        $storage_table_name = $sub_service_form_field[0]->storage_table_name;

        $column_name = array("id");
        foreach($sub_service_form_field as $field){
            array_push($column_name,$field->field_column_name);
        }

        $data = DB::table($storage_table_name)->get($column_name);
        return response()->json(["result" => $data]);

    }


    //view by id sub service form fields entry of data by user for that particular form
    public function view_by_id_custom_sub_service_form_field_data_entry($id,$sub_service_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->where('status',1)->orderBy('order_number')->get();
        if(count($sub_service_form_field)==0){
            return response()->json(["error"=>"no fields available for this form"], 200);
        }

        $storage_table_name = $sub_service_form_field[0]->storage_table_name;

        $column_name = array("id");
        foreach($sub_service_form_field as $field){
            array_push($column_name,$field->field_column_name);
        }

        $data = DB::table($storage_table_name)->where('id', $id)->get($column_name);
        if(count($data)!=0){
            return response()->json(["result" => $data[0]]);
        }
        return response()->json(["result" => $data]);

    }

     //delete by id sub service form fields entry of data by user for that particular form
     public function delete_by_id_custom_sub_service_form_field_data_entry($id,$sub_service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->where('status',1)->orderBy('order_number')->get();
        if(count($sub_service_form_field)==0){
            return response()->json(["error"=>"no fields available for this form"], 200);
        }

        $storage_table_name = $sub_service_form_field[0]->storage_table_name;

        $column_name = array("id");
        foreach($sub_service_form_field as $field){
            array_push($column_name,$field->field_column_name);
        }

        $data = DB::table($storage_table_name)->where('id', $id)->get("user_id");
        if(count($data)!=0){
            if($data[0]->user_id == $user->id){
                DB::table($storage_table_name)->where('id', $id)->delete();
                return response()->json(["result" => "Data deleted successfully"]);
            }else{
                return response()->json(["error" => "Not eligible for the action"]);
            }
        }else{
            return response()->json(["error" => "Invalid Id"]);
        }
        
     }

     //edit by id sub service form fields entry of data by user for that particular form
    public function edit_by_id_custom_sub_service_form_field_data_entry(Request $req,$id,$sub_service_id){

        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $sub_service = Sub_Service::find($sub_service_id);
        if(!$sub_service){
            return response()->json(["error"=>"invalid sub-service id"], 200);
        }

        $sub_service_form_field = Sub_Service_Form_Field::where('sub_service_id',$sub_service_id)->where('status',1)->orderBy('order_number')->get();
        if(count($sub_service_form_field)==0){
            return response()->json(["error"=>"no fields available for this form"], 200);
        }

        $storage_table_name = $sub_service_form_field[0]->storage_table_name;

        $column_name = array("id", "user_id");
        $new_column_name = array();
        foreach($sub_service_form_field as $field){
            array_push($column_name,$field->field_column_name);
            array_push($new_column_name,$field->field_column_name);
        }

        $data = DB::table($storage_table_name)->where('id', $id)->get($column_name);
        if(count($data)!=0){
            if($data[0]->user_id == $user->id){
                $data = array();

                foreach ($sub_service_form_field as $obj) {
                    if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && ($obj->field_type=="attatchment")){
                        if($obj->mandatory==1){
                            if(isset(($_FILES[$obj->field_column_name]))){
                                $extensions= array("jpeg","jpg","png");
                                $file_ext=strip_tags($req->file($obj->field_column_name)->extension());
                                if(in_array($file_ext,$extensions)=== false){
                                    $validator = Validator::make([], []); // Empty data and rules fields
                                    $validator->errors()->add('error', $obj->display_name.' supports .jpg, .png and .jpeg file');
                                    return $validator->errors();
                                }else{
                                    $newImage = time().'-'.strip_tags($req->file($obj->field_column_name)->getClientOriginalName());
                                    $req->file($obj->field_column_name)->move(public_path('service/form'), $newImage);
                                    $data[$obj->field_column_name] =  $newImage;
                                }
                            }else{
                                $validator = Validator::make([], []); // Empty data and rules fields
                                $validator->errors()->add('error', $obj->display_name.' requires a file to be uploaded');
                                return $validator->errors();
                            }
                            
                        }else{
                            if(isset(($_FILES[$obj->field_column_name]))){
                                $extensions= array("jpeg","jpg","png");
                                $file_ext=strip_tags($req->file($obj->field_column_name)->extension());
                                if(in_array($file_ext,$extensions)=== false){
                                    $validator = Validator::make([], []); // Empty data and rules fields
                                    $validator->errors()->add('error', $obj->display_name.' supports .jpg, .png and .jpeg file');
                                    return $validator->errors();
                                }else{
                                    $newImage = time().'-'.strip_tags($req->file($obj->field_column_name)->getClientOriginalName());
                                    $req->file($obj->field_column_name)->move(public_path('service/form'), $newImage);
                                    $data[$obj->field_column_name] =  $newImage;
                                }
                            }else{
                                $data[$obj->field_column_name] =  null;
                            }
                        }
                    
                    }
                    else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="email")||($obj->field_type=="profile email"))){
                        if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", strip_tags($req->input($obj->field_column_name))) ){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', 'invalid '.$obj->display_name.' format' );
                            return $validator->errors();
                        }else{
                            if(!empty(strip_tags($req->input($obj->field_column_name)))){
                                $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                            }else{
                                $data[$obj->field_column_name] =  null;
                            }
                        }
                        
                    }
                    else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="mobile")||($obj->field_type=="profile mobile")||($obj->field_type=="number")||($obj->field_type=="profile whatsapp"))){
                        if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^[0-9][0-9 ]*$/", strip_tags($req->input($obj->field_column_name))) ){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' can contain only numeric characters' );
                            return $validator->errors();
                        }else{
                            if(!empty(strip_tags($req->input($obj->field_column_name)))){
                                $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                            }else{
                                $data[$obj->field_column_name] =  null;
                            }
                            
                        }
                        
                    }
                    else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="profile name")||($obj->field_type=="text"))){
                        if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^[a-zA-Z][a-zA-Z ]*$/", strip_tags($req->input($obj->field_column_name))) ){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' can contain only letters and spaces' );
                            return $validator->errors();
                        }else{
                            if(!empty(strip_tags($req->input($obj->field_column_name)))){
                                $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                            }else{
                                $data[$obj->field_column_name] =  null;
                            }
                            
                        }
                        
                    }
                    else if(($obj->field_column_name!="sub_service_id") && ($obj->field_column_name!="service_id") && ($obj->field_column_name!="user_id") && (($obj->field_type=="description")||($obj->field_type=="date"))){
                        if($obj->mandatory==1 && strlen(strip_tags($req->input($obj->field_column_name)))==0){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' cannot be left blank');
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && strlen(strip_tags($req->input($obj->field_column_name)))>$obj->length){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' can have maximum '.$obj->length.' characters' );
                            return $validator->errors();
                        }else if(!empty(strip_tags($req->input($obj->field_column_name))) && !preg_match("/^[a-z 0-9~%.:_\@\-\/\&+=,]+$/i", strip_tags($req->input($obj->field_column_name))) ){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('error', $obj->display_name.' cannot contain special characters' );
                            return $validator->errors();
                        }else{
                            if(!empty(strip_tags($req->input($obj->field_column_name)))){
                                $data[$obj->field_column_name] =  strip_tags($req->input($obj->field_column_name));
                            }else{
                                $data[$obj->field_column_name] =  null;
                            }
                            
                        }
                        
                    }  
                    
                }

                $update_data = array();
                foreach($data as $data=>$val){
                    $update_data[$data] = $val;
                }
                DB::table($storage_table_name)->where('id', $id)->update($update_data);
                return response()->json(["result" => "Data updated successfully"]);

            }else{
                return response()->json(["error" => "Not eligible for the action"]);
            }
        }
        return response()->json(["error" => "No data available"]);

    }

         

}
