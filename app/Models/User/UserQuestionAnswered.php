<?php
namespace App\Models\User;

use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class UserQuestionAnswered extends Model
{
    use HasFactory;

    protected $primaryKey = ['user_id', 'question_id'];
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'question_id',
        'alternative',
        'error_notebook'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Question()
    {
        return $this->belongsTo(Question::class);
    }

    public function UserQuestionAnnotation()
    {
        return $this->hasMany(UserQuestionAnnotation::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\UserQuestionAnsweredFactory::new();
    }
}
