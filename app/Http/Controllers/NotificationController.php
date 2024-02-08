<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Notification;
use App\Models\AccountDetails;

class NotificationController extends APIController
{
    //

    public function create(Request $request)
    {
        $data = $request->all();
        $notification = new Notification();
        $notification->id = Str::orderedUuid();
        $notification->user_id = $data['to_user'];
        $notification->notif_message = $data['message'];
        $notification->save();

        $this->response['data'] = 'Inserted';
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
            $this->response['error'] = " Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->user_id = $data['to_user'];
            $query->notif_message = $data['message'];
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
            $this->response['error'] = " Not Found";
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
