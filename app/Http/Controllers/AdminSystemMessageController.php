<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\AdminSystemMessage;

class AdminSystemMessageController extends Controller
{
    //
    public function create(Request $request){
        
        $Uuid = Str::orderedUuid();
        try{
        $data = $request->all();
        $query = new AdminSystemMessage();
        $query->id = $Uuid;
        $query->message_by = $data['message_by'];
        $query->system_message = $data['system_message'];
        $query->save();
        }catch (\Throwable $th){
            $message = $th->getMessage();
            $this->response['error'] = mb_convert_encoding($message, 'UTF-8', 'auto');
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
    public function delete(Request $request){
        $data = $request->all();
        $query = AdminSystemMessage::find($data['id']);
        if(!$query){
            $this->response['error'] = "Message Not Found";
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
    public function retrieveAll(Request $request){
        $response = AdminSystemMessage::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = AdminSystemMessage::where($data['col'], '=' ,$data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function update(Request $request){
        $data = $request->all();
        $query = AdminSystemMessage::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            $query->message_by = $data['message_by'];
            $query->system_message = $data['system_message'];
            $query->save();
        }
    }
}
