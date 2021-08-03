<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    //create tetimonial
    public function create(Request $req){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $rules = array(
            "name" => "required|min:3",
            "image" => "required|mimes:jpg,png,jpeg|max:2048",
            "description" => "required",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else if(!$req->file('image')->isValid()){
            return response()->json(["error"=>"invalid image"], 200);
        }else{
            $newImage = time().'-'.$req->image->getClientOriginalName();
            $req->image->move(public_path('testimonial'), $newImage);
            $service = new Testimonial;
            $service->name = strip_tags($req->name);
            $service->image = $newImage;
            $service->description = strip_tags($req->description);
            $result = $service->save();
            if($result){
                return response()->json(["result"=>"testimonial created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

    //update testimonial
    public function update(Request $req, $id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Testimonial::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid testimonial id"], 200);
        }

        

        $rules = array(
            "name" => "required|min:3",
            "description" => "required",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            $service->name = strip_tags($req->name);
            $service->description = strip_tags($req->description);
            $result = $service->save();
            
            if($result){
                return response()->json(["result"=>"Testimonial updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

     //update testimonial logo
     public function update_logo(Request $req, $id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Testimonial::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid testimonial id"], 200);
        }

        $rules = array(
            "image" => "required|mimes:jpg,png,jpeg|max:2048",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            unlink(public_path('testimonial/'.$service->image));
            $newImage = time().'-'.$req->image->getClientOriginalName();
            $req->image->move(public_path('testimonial'), $newImage);
            $service->image = $newImage;
            $result = $service->save();
            
            if($result){
                return response()->json(["result"=>"Testimonial updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

    //delete testimonial
    public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        if($user->is_admin==0){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Testimonial::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid testimonial id"], 200);
        }

        unlink(public_path('testimonial/'.$service->image));
        $service->delete();
        return response()->json(["result"=>"Department deleted"], 200);
    }

    //view testimonial
    public function view(){
        $testimonial = Testimonial::all();
        return response()->json(["result"=>$testimonial], 200);
    }

    //view services by id
    public function viewById($id){
        $testimonial = Testimonial::find($id);
        return response()->json(["result"=>$testimonial], 200);
     }


}
