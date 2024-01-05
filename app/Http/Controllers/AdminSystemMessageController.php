<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\AdminSystemMessage;
use App\Models\AccountDetails; //has user accounts for now, clarify on what accs to use

class AdminSystemMessageController extends Controller
{
    //
    
    public function index()
    {
        return 'this is the system message controller for admins';
    }

    public function create(Request $request)
    {
        //$testData = "very good job!";
        $data = $request->all();
        $systemmessage = new SystemMessage();
        $systemmessage->id = Str::orderedUuid();
        $systemmessage->message = $data['admin_id'];
        $systemmessage->message = $data['scholar_id'];
        $systemmessage->message = $data['system_message'];
        $comments->save();

        $this->response['data'] = 'SystemMessage added successfully';
        return $this->getResponse();
        
        // Comments::create(['message' => $testData]);

        // return response()->json(['message' => 'Test data added successfully'], 201);
        //return $testData;
    }

    public function retrievebyParameter(Request $request)    {
        
        // $comment = Item::findOrFail($id);
        // return response()->json($comment);
    
        $data = $request->all();
        $response = SystemMessage::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
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
        $query = SystemMessage::find($data['id']);
        if(!$query){
            $this->response['error'] = "Message Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->message = $data['admin_id'];
            $query->message = $data['scholar_id'];
            $query->message = $data['system_message'];
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

    public function delete(Request $request) {
        
        $data = $request->all();
        $query = SystemMessage::find($data['id']);
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
