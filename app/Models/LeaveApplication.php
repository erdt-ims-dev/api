<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveApplication extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'scholar_leave_application';
    protected $fillable = [
        'user_id',
        'leave_start',
        'leave_end',
        'leave_reason',
        'status',
        ];
}
