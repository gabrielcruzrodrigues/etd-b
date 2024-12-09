<?php

namespace Modules\StudyPlain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\StudyPlain\Models\RegisterStudy;

class SpacedEstudie extends Model
{
    protected $fillable = [
        'date',
        'register_study',
        'completed',
        'conclusion_study_time'
    ];

    public function RegisterStudy()
    {
        return $this->belongsTo(RegisterStudy::class);
    }
}
