<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends APIController
{
    public function retrieveAll(){
        $response = User::withTrashed()->get();;
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '=', $data['value'])->get();
        if ($response->isNotEmpty()) {
            $this->response['data'] = $response[0];
            $this->response['status'] = 200;
        } else {
            // Handle the case where no results are found
            $this->response['error'] = "User not found";
            $this->response['status'] = 404;
        }
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function update(Request $request) {
        $data = $request->all();
        $query = User::find($data['id']);
        if (!$query) {
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if ($query) {
            $query->{$data['col']} = $data['value'];
            $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    public function delete(Request $request) {
        
        $data = $request->all();
        $query = User::find($data['id']);
        if(!$query){
            $this->response['error'] = "User Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->status = 'deactivated';
            $query->save();
            $query->delete();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
}
