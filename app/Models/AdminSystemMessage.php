<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminSystemMessage extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'admin_system_message';
    protected $fillable = [
        'message_by',
        'system_message',
        ];
}
