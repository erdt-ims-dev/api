<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholar extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar';
    protected $fillable = [
        'user_id',
        'scholar_request_id',
        'scholar_task_id',
        'scholar_portfolio_id',
        'scholar_leave_app_id',
        ];
}
