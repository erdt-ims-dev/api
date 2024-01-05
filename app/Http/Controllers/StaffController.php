<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
class StaffController extends APIController
{
    //
    function create(Request $request){
        $data = $request->all();
        $generateID = Str::orderedUuid();
        $staff = new Staff();
        $staff->id = $generateID;
    }
    
}
