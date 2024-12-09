<?php

namespace Modules\Reports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Question\Models\Question;
use Modules\FlashCard\Models\FlashCard;


class Reports extends Model
{
    protected $fillable = [
        'entity_type',
        'flashcard_id',
        'question_id',
        'report_type',
        'comment',
    ];

    public function FlashCards()
    {
        return $this->hasMany(FlashCard::class);
    }

    public function Questions()
    {
        return $this->hasMany(Question::class);
    }
}
