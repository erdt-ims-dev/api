<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffApplicantManagement extends Model
{
    use HasFactory, SoftDeletes, UUID;
    protected $table = 'staff_applicant_management';
    protected $fillable = [
        'scholarrequest_id',
        'staff_id',
        ];
}
