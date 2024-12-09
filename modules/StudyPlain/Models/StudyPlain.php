<?php

namespace Modules\StudyPlain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class StudyPlain extends Model
{
    use HasFactory;

    protected $table = 'study_plain';

    protected $fillable = [
        'start',
        'exame_date',
        'desired_course',
        'school_year',
        'dificulty_matters',
        'day_off',
        'hours_study_day',
        'exame_expirence',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function ReviewCards()
    {
        return $this->hasMany(ReviewCard::class);
    }

    public function RegisterStudies()
    {
        return $this->hasMany(RegisterStudy::class);
    }
}
