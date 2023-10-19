<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDetails extends Model
{
    use HasFactory;
    protected $table = 'account_details';
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'program',
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
