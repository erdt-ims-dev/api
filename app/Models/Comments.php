<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comments extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'comments';
    protected $fillable = [
        'message',
        ];

    // $message = new Message();
    // $message->message = "This is a test case data";
    // $message->save();
}
