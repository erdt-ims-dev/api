<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class Notification extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'notification';
    protected $fillable = [
        'user_id',
        'notif_message',
        ];
}
