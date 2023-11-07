<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class ScholarPortfolio extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'scholar_portfolio';
    protected $fillable = [
        'user_id',
        'study',
        'study_category',
        'publish_type',
        ];
}
