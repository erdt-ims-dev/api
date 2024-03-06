<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends APIController
{
    public function retrieveAll(){
        $response = User::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = User::where($data['col'], '=' ,$data['value'])->get();
        if ($response->isEmpty()) {
            // If no results are found, return an appropriate response
            $this->response['error'] = 'No matching records found.';
            $this->response['status'] = 404;
        } else {
            // If results are found, return the first record
            // $this->response['data'] = $response[0];
            // $this->response['status'] = 200;
            
            // return all records
            $this->response['data'] = $response;
            $this->response['status'] = 200;
        
        }
    
        return $this->getResponse();
    }
}
