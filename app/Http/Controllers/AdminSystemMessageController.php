<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\AdminSystemMessage;
use App\Models\User;
use App\Models\AccountDetails;

class AdminSystemMessageController extends APIController
{
    //
    public function create(Request $request){
        
        try{
        $data = $request->all();
        $query = new AdminSystemMessage();
        $query->message_by = $data['message_by'];
        $query->message_title = $data['message_title'];
        $query->message_body = $data['message_body'];
        $query->status = "active";
        $query->save();
        }catch (\Throwable $th){
            $message = $th->getMessage();
            $this->response['error'] = mb_convert_encoding($message, 'UTF-8', 'auto');
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
        $this->response['data'] = 'Message Created';
        $this->response['details'] = $query;
        return $this->getResponse();
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
            $query->status = 'inactive';
            $query->save();
            $query->delete();
            $this->response['data'] = 'Deleted';
            $this->response['status'] = 200;
            return $this->getResponse();
        }

    }
    public function retrieveAll(Request $request){
        $response = AdminSystemMessage::withTrashed()->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function paginate(Request $request){
        $limit = $request->input('limit', 10); // Default limit is 10
        $offset = $request->input('offset', 0); // Default offset is 0

        $items = AdminSystemMessage::skip($offset)->take($limit)->get();
        $total = AdminSystemMessage::count(); // Total number of accounts

        $this->response['data'] = [
            'items' => $items,
            'total' => $total,
        ];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    // public function paginate(Request $request) {
    //     // Get the limit and page, with defaults and validation
    //     $limit = max((int) $request->input('limit', 10), 1); // Default limit is 10, minimum 1
    //     $page = max((int) $request->input('page', 1), 1);    // Default page is 1, minimum 1
    //     $offset = ($page - 1) * $limit;
    
    //     // Fetch paginated items
    //     $items = AdminSystemMessage::skip($offset)->take($limit)->get();
    //     $total = AdminSystemMessage::count();
    
    //     // Construct paginated response
    //     $this->response['data'] = [
    //         'items' => $items,
    //         'total' => $total,
    //         'page' => $page,
    //         'totalPages' => ceil($total / $limit), // Total pages for client reference
    //     ];
    //     $this->response['status'] = 200;
    
    //     return $this->getResponse();
    // }
    
    public function retrieveOneByParameter(Request $request)    {

        $data = $request->all();
        $response = AdminSystemMessage::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = AdminSystemMessage::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
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
            $query->message_title = $data['message_title'];
            $query-> message_body = $data['message_body'];
            $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

    public function retrieveViaDashboard(Request $request){
        // First, retrieve the latest 10 active messages
        $messages = AdminSystemMessage::where('status', '=', 'active')
                                      ->orderBy('created_at', 'desc')
                                      ->take(10)
                                      ->get();
    
        $mergedData = [];
    
        foreach ($messages as $message) {
            // Use the email from the message to find the corresponding user ID
            $userId = User::where('email', $message->message_by)->first()->id;
    
            // Once you have the user ID, use it to find the profile picture in AccountDetails
            $accountDetail = AccountDetails::where('user_id', $userId)->first();
    
            // Merge the message with its profile picture into a single object
            $mergedEntry = [
                'message' => [
                    'id' => $message->id,
                    'message_by' => $message->message_by,
                    'message_title' => $message->message_title,
                    'message_body' => $message->message_body,
                    'status' => $message->status,
                    'created_at' => $message->created_at,
                ],
                'profilePicture' => $accountDetail? $accountDetail->profile_picture : null,
            ];
    
            // Add the merged entry to the array
            $mergedData[] = $mergedEntry;
        }
    
        // Prepare the response
        $this->response['data'] = $mergedData;
        $this->response['status'] = 200;
    
        return $this->getResponse();
    }
}
