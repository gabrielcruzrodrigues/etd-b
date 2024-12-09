<?php

namespace Modules\FlashCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class UserFlashCardAnswered extends Model
{
    protected $fillable = [
        'user_id',
        'flashcard_id',
        'hit_level',
        'matter_id',
        'content_id',
        'topics',
        'subtopics',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function FlashCard()
    {
        return $this->belongsTo(FlashCard::class);
    }

    public function UserFlashCardAnnotations()
    {
        return $this->hasMany(UserFlashCardAnnotation::class);
    }
}
