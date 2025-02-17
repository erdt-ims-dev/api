<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserForgotPassword extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_forgot_password';
    protected $fillable = [
        'user_id',
        'token',
        'status',
        ];
}
