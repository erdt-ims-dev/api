<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplicationStatus extends Model
{
    use HasFactory;
    protected $table = 'scholar_leave_application_status';
    protected $fillable = [
        'scholar_leave_app_id',
        'user_id',
        'comment_id',
        'application_status',
        'application_letter'
        ];
}
