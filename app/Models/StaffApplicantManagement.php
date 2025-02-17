<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffApplicantManagement extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'staff_applicant_management';
    protected $fillable = [
        'scholar_request_id',
        'endorsed_by',
        ];
}
