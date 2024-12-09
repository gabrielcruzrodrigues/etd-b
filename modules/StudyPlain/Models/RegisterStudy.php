<?php

namespace Modules\StudyPlain\Models;

use App\SpacedEstudie;
use Illuminate\Database\Eloquent\Model;
use Modules\StudyPlain\Models\StudyPlain;

class RegisterStudy extends Model
{
  protected $table = 'register_study';

  protected $fillable = [
    'date',
    'study_plain_id',
    'matter',
    'content',
    'topic_1',
    'topic_2',
    'conclusion_study_time'
  ];

  public function studyPlain()
  {
    return $this->belongsTo(StudyPlain::class);
  }

  // Caso queira tratar as datas (por exemplo, campo 'date' como objeto de data do Carbon)
  protected $dates = ['date'];

  // Caso queira tratar o campo de tempo como objeto de tempo
  protected $casts = [
    'conclusion_study_time' => 'time',
  ];

  public function SpacedEstudies()
  {
    return $this->hasMany(SpacedEstudie::class);
  }
}
