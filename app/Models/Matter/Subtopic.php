<?php

namespace App\Models\Matter;

use App\Models\Matter\Topic;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subtopic extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'topic_id'];

    public function Topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function Questions()
    {
        return $this->hasMany(Question::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\SubtopicFactory::new();
    }
}
