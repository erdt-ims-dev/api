<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comments;

class CommentsController extends Controller
{
    
    public function index()
    {
        return 'this is the comments controller';
    }

    public function addComments(Request $request)
    {
        //$testData = "very good job!";
        $data = $request->all();
        $comments = new Comments();
        $comments->message = $data['message'];
        $comments->save();

        $this->response['data'] = 'Comment added successfully';
        return $this->getResponse();
        
        // Comments::create(['message' => $testData]);

        // return response()->json(['message' => 'Test data added successfully'], 201);
        //return $testData;
    }
}
