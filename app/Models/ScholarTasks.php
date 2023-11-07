<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class ScholarTasks extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'scholar_tasks';
    protected $fillable = [
        'user_id',
        'midterm_assessment',
        'final_assessment',
        'approval_status',
        ];
}
