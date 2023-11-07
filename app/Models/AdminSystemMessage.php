<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class AdminSystemMessage extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'admin_system_message';
    protected $fillable = [
        'admin_id',
        'scholar_id',
        'system_message',
        ];
}
