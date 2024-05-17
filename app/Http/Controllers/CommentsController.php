<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Comments;
use App\Models\AccountDetails;
use App\Models\ScholarRequestApplication;

class CommentsController extends APIController
{

    public function create(Request $request)
    {
        $data = $request->all();
        $comments = new Comments();
        $comments->comment_by = $data['comment_by'];
        $comments->message = $data['message'];
        $comments->save();

        $this->response['data'] = $comments;
        return $this->getResponse();
    }
    // Create comments in Applications tab

    public function createViaApplication(Request $request)
    {
        $data = $request->all();
        $query = ScholarRequestApplication::where('account_details_id', '=', $data['id'])->first();
        if(!$query){
            $this->response['error'] = "ID Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        // Create new comment in CommentTable
        $comments = new Comments();
        $comments->comment_by = $data['comment_by'];
        $comments->message = $data['message'];
        $comments->save();

        // Link created entry to Scholar Request Application Table
        $query->comment_id = $comments->id;
        $query->save();
        $this->response['comments'] = $comments;
        $this->response['scholar_req'] = $query;
        return $this->getResponse();
    }
    public function retrieveWithAccountDetails(Request $request)    {
    
        $data = $request->all();
        $query = ScholarRequestApplication::where("account_details_id", '=', $data['id'])->first();
        $res = Comments::where('id', '=', $query->comment_id)->first();
        $this->response['data'] = $res;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = Comments::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = Comments::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function retrieveAll() {

        $response = Comments::all();

        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function update(Request $request) {

        $data = $request->all();
        $query = Comments::find($data['id']);
        if(!$query){
            $this->response['error'] = "Comment Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->message = $data['message'];
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

    public function delete(Request $request) {
        
        $data = $request->all();
        $query = Comments::find($data['id']);
        if(!$query){
            $this->response['error'] = "Comment Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->delete();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

}
