<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarList extends Model
{
    use HasFactory;
    protected $table = 'scholar_list';
    protected $fillable = [
        'scholar_id',
        ];
}
