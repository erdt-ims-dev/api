<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSystemMessage extends Model
{
    use HasFactory;
    protected $table = 'admin_system_message';
    protected $fillable = [
        'admin_id',
        'scholar_id',
        'system_message',
        ];
}
