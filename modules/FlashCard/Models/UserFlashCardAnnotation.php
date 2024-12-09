<?php

namespace Modules\FlashCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class UserFlashCardAnnotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_flashcard_answered_id',
        'annotation'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function UserFlashCardAnswered()
    {
        return $this->belongsTo(UserFlashCardAnswered::class);
    }
}
