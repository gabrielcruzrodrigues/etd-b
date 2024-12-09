<?php

namespace Modules\Lesson\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class LessonUserRating extends Model
{
  use HasFactory;

  protected $fillable = [
    'lesson_id',
    'user_id',
    'note',
  ];

  public function lesson()
  {
    return $this->belongsTo(Lesson::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
