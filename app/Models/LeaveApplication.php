<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;
    protected $table = 'scholar_leave_application';
    protected $fillable = [
        'user_id',
        'leave_start',
        'leave_end',
        'leave_reason',
        'status',
        'profile_picture',
        'birth_certificate',
        'tor',
        'narrative_essay',
        'recommendation_letter',
        'medical_certificate',
        'nbi_clearance',
        'admission_notice',
        'account_type'];
}
