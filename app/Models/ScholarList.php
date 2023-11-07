<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class ScholarList extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'scholar_list';
    protected $fillable = [
        'scholar_id',
        ];
}
