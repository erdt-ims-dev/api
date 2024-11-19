<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\LeaveApplication;
use App\Models\ScholarRequestApplication;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Storage;

class ReportsController extends APIController
{
    //
    public function generateReport(Request $request) {
        $data = $request->all();
        $entries = []; // Initialize entries array
        $userList = []; // Initialize userList array for ongoing scholars
    
        if ($data['status'] === 'on leave') {
            // Use user table to find entries that are on leave
            $users = User::where('status', '=', 'On Leave')->get();
    
            foreach ($users as $user) {
                // Base query for leave applications
                $leaveQuery = LeaveApplication::where('user_id', '=', $user->id)
                                ->where('status', '=', 'Approved');
    
                // Apply additional filters if provided and not set to "all"
                if (!empty($data['year']) && $data['year'] !== 'all') {
                    $leaveQuery->where('year', '=', $data['year']);
                }
    
                if (!empty($data['semester']) && $data['semester'] !== 'all') {
                    $leaveQuery->where('semester', '=', $data['semester']);
                }
    
                // Fetch the leave applications after applying all filters
                $leaveApplications = $leaveQuery->get();
    
                foreach ($leaveApplications as $leave) {
                    // Store relevant data into entries
                    $entries[] = [
                        'user_name' => $user->first_name . ' ' . $user->last_name,
                        'leave_duration' => [
                            'semester' => $leave->semester,
                            'year' => $leave->year,
                        ],
                        'leave_status' => $leave->status,
                    ];
                }
            }
    
            // Add leaves data to the response
            $this->response['leaves'] = $entries;
        }
    
        if ($data['status'] === 'on going') {
            // Use user table to find entries that are ongoing scholars
            $users = User::where('status', '=', 'active')
                         ->where('account_type', '=', 'scholar')
                         ->get();
    
            foreach ($users as $user) {
                // Store user data into userList
                $userList[] = [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'account_type' => $user->account_type,
                ];
            }
    
            // Add scholars data to the response
            $this->response['scholars'] = $userList;
        }
    
        // Send response
        $this->response['status'] = 200;
        $this->response['data'] = $entries; // 'entries' remains for backward compatibility
        return $this->getResponse();
    }
    
    
    
}
