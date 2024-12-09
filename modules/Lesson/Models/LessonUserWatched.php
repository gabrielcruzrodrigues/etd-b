<?php

namespace Modules\Lesson\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Lesson\Models\Lesson;
use Modules\User\Models\User;

class LessonUserWatched extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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