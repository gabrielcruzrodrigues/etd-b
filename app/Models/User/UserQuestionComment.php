<?php
namespace App\Models\User;
;

use App\Models\Question\Question;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserQuestionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'comment',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Question()
    {
        return $this->belongsTo(Question::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\UserQuestionCommentFactory::new();
    }
}
