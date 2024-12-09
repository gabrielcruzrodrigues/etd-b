<?php

namespace Modules\FlashCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\UserQuestionAnswered;

class FlashCard extends Model
{
    protected $fillable = [
        'code',
        'question',
        'path_image',
        'answer',
        'matter_id',
        'content_id',
        'topic_id',
        'subtopics_id',
        'visibility',
        'state',
    ];

    public function UserFlashCreated()
    {
        return $this->hasMany(UserFlashCreated::class);
    }

    public function UserQuestionAnswereds()
    {
        return $this->hasMany(UserQuestionAnswered::class);
    }
}
