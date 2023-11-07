<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class Staff extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'staff';
    protected $fillable = ['user_id','password', 'account_type'];
}
