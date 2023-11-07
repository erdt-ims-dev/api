<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class LeaveApplicationStatus extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'scholar_leave_application_status';
    protected $fillable = [
        'scholar_leave_app_id',
        'user_id',
        'comment_id',
        'application_status',
        'application_letter'
        ];
}
