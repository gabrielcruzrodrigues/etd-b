<?php

namespace App\Models\Matter;

use App\Models\Content\Content;
use App\Models\Matter\Subtopic;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'content_id'];

    public function Content()
    {
        return $this->belongsTo(Content::class);
    }

    public function Subtopics()
    {
        return $this->hasMany(Subtopic::class);
    }

    public function Questions()
    {
        return $this->hasMany(Question::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\TopicFactory::new();
    }
}
