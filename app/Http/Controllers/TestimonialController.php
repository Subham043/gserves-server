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

    //view testimonial
    public function view(){
        $testimonial = Testimonial::all();
        return response()->json(["result"=>$testimonial], 200);
    }


}
