<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    //create forum
    public function create(Request $req){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        

        $rules = array(
            "message" => "required",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $service = new Forum;
            $service->message = strip_tags($req->message);
            $service->user_id = $user->id;
            $result = $service->save();
            if($result){
                return response()->json(["result"=>"forum created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

    //update forum
    public function update(Request $req, $id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        

        $service = Forum::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid forum id"], 200);
        }

        if($user->id!=$service->user_id){
            return response()->json(["error"=>"Not the owner of the forum message"], 200);
        }

        $rules = array(
            "message" => "required",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $service->message = strip_tags($req->message);
            $result = $service->save();
            
            if($result){
                return response()->json(["result"=>"forum updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

     //delete forum
     public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        

        $service = Forum::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid forum id"], 200);
        }

        if($user->id!=$service->user_id){
            return response()->json(["error"=>"Not the owner of the forum message"], 200);
        }

        $service->delete();
        return response()->json(["result"=>"forum deleted"], 200);
    }

    //view forum
    public function view(){
        $forum = Forum::all();
        return response()->json(["result"=>$forum], 200);
    }


}
