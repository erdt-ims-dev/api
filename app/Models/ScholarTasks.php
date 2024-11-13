<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarTasks extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar_tasks';
    protected $fillable = [
        'user_id',
        'scholar_id',
        'year',
        'semester',
        'type',
        'file',
        'approval_status',
        ];
}
