<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\Forum_Reply;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ForumReplyController extends Controller
{
    //create forum reply
    public function create(Request $req, $forum_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $forum = Forum::find($forum_id);
        if(!$forum){
            return response()->json(["error"=>"invalid forum id"], 200);
        }
        

        $rules = array(
            "message" => "required",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $service = new Forum_Reply;
            $service->message = strip_tags($req->message);
            $service->user_id = $user->id;
            $service->forum_id = $forum->id;
            $result = $service->save();
            if($result){
                return response()->json(["result"=>"forum reply created"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }

       
    }

     //update forum reply
     public function update(Request $req, $id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        

        $service = Forum_Reply::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid forum reply id"], 200);
        }

        if($user->id!=$service->user_id){
            return response()->json(["error"=>"Not the owner of the forum reply"], 200);
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
                return response()->json(["result"=>"forum reply updated"], 201);
            }else{
                return response()->json(["error"=>"something went wrong. Please try again"], 200);
            }
        }
    }

     //delete forum reply
     public function delete($id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }
        

        $service = Forum_Reply::find($id);
        if(!$service){
            return response()->json(["error"=>"invalid forum reply id"], 200);
        }

        if($user->id!=$service->user_id){
            return response()->json(["error"=>"Not the owner of the forum reply"], 200);
        }

        $service->delete();
        return response()->json(["result"=>"forum reply deleted"], 200);
    }

     //view forum reply
     public function view($forum_id){
        $forum = Forum_Reply::where('forum_id', $forum_id)->get();
        return response()->json(["result"=>$forum], 200);
    }



}
