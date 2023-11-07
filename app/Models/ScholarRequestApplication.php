<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarRequestApplication extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar_request_application';
    protected $fillable = [
        'account_details_id',
        'user_id',
        'status',
        'comment_id'
        ];
}
