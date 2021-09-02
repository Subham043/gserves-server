<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    //create forum
    public function create(Request $req, $service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Service::find($service_id);
        if(!$service){
            return response()->json(["error"=>"invalid service id"], 200);
        }
        

        $rules = array(
            "message" => "required|regex:/^[a-z 0-9~%.:_\@\-\/\&+=,]+$/i",
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $forum = new Forum;
            $forum->message = strip_tags($req->message);
            $forum->user_id = $user->id;
            $forum->service_id = $service->id;
            $result = $forum->save();
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
        

        $forum = Forum::find($id);
        if(!$forum){
            return response()->json(["error"=>"invalid forum id"], 200);
        }

        if($user->id!=$forum->user_id){
            return response()->json(["error"=>"Not the owner of the forum message"], 200);
        }

        $rules = array(
            "message" => "required|regex:/^[a-z 0-9~%.:_\@\-\/\&+=,]+$/i",
        );
        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return $validator->errors();
        }else{
            
            $forum->message = strip_tags($req->message);
            $result = $forum->save();
            
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
        

        $forum = Forum::find($id);
        if(!$forum){
            return response()->json(["error"=>"invalid forum id"], 200);
        }

        if($user->id!=$forum->user_id){
            return response()->json(["error"=>"Not the owner of the forum message"], 200);
        }

        $forum->delete();
        return response()->json(["result"=>"forum deleted"], 200);
    }

    //view forum
    public function view(){
        $forum = Forum::all();
        return response()->json(["result"=>$forum], 200);
    }

    //view all forum with reply
    public function viewAll($service_id){
        $user = auth()->user();
        if(!$user){
            return response()->json(["error"=>"unauthorised"], 200);
        }

        $service = Service::find($service_id);
        if(!$service){
            return response()->json(["error"=>"invalid service id"], 200);
        }

        $forums = DB::table('forums')->where('service_id',$service_id)->get()->toArray();
        $forum_replies = DB::table('forum__replies')->where('service_id',$service_id)->get()->toArray();
        
        foreach($forums as &$forum)
        {
            $forum->forum_replies = array();
            foreach($forum_replies as &$forum_reply)
            {
                if($forum_reply->forum_id==$forum->id){
                    array_push($forum->forum_replies, $forum_reply);
                }
            }
            
        }

        return response()->json(["result"=>$forums], 200);
    }


}
