<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVerificationToken extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'user_verification_token';
    protected $fillable = [
        'user_id',
        'status',
        ];
}
