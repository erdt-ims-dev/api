<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarList extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar_list';
    protected $fillable = [
        'scholar_id',
        ];
}
