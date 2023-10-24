<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarTasks extends Model
{
    use HasFactory;
    protected $table = 'scholar_tasks';
    protected $fillable = [
        'user_id',
        'midterm_assessment',
        'final_assessment',
        'approval_status',
        ];
}
