<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Notification;
use App\Models\AccountDetails;

class NotificationController extends Controller
{
    //
    public function index()
    {
        return 'this is the comments controller';
    }

    public function create(Request $request)
    {
        //$testData = "very good job!";
        $data = $request->all();
        $notification = new Notification();
        $notification->id = Str::orderedUuid();
        $notification->message = $data['user_id'];
        $notification->message = $data['notif_message'];
        $notification->save();

        $this->response['data'] = 'Comment added successfully';
        return $this->getResponse();
        
    }

    public function retrievebyParameter(Request $request)    {
        
        // $comment = Item::findOrFail($id);
        // return response()->json($comment);
    
        $data = $request->all();
        $response = Notification::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function retrieveAll() {

        $response = Notification::all();

        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function update(Request $request) {

        $data = $request->all();
        $query = Notification::find($data['id']);
        if(!$query){
            $this->response['error'] = "Comment Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->message = $data['user_id'];
            $query->message = $data['notif_message'];
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

    public function delete(Request $request) {
        
        $data = $request->all();
        $query = Notification::find($data['id']);
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
