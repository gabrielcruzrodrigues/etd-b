<?php
namespace App\Models\Question;

use App\Models\Content\Content;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Models\User\UserQuestionAnswered;
use App\Models\User\UserQuestionComment;
use App\Models\Year\Year;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'alternative_a',
        'alternative_b',
        'alternative_c',
        'alternative_d',
        'alternative_e',
        'answer',
        'alternative_has_html',
        'matter_id',
        'content_id',
        'topic_id',
        'subtopic_id',
        'difficulty',
        'original_code',
        'year_id',
        'institution_id',
        'code',
        'state',
    ];

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class);
    }

    public function answers()
    {
        return $this->hasMany(UserQuestionAnswered::class);
    }

    public function userQuestionComments()
    {
        return $this->hasMany(UserQuestionComment::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\QuestionFactory::new();
    }
}

