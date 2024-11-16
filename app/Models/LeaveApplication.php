<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveApplication extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar_leave_application';
    protected $fillable = [
        'user_id',
        'leave_start',
        'comment_id',
        'leave_end',
        'leave_letter',
        'status',
        ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function accountDetails()
    {
        return $this->hasOneThrough(AccountDetails::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }
}
