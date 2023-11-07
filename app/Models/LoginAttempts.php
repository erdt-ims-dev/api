<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class LoginAttempts extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'login_attempts';
    protected $fillable = [
        'account_id',
        'attempts',
        ];
}
