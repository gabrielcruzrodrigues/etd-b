<?php
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserQuestionAnnotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'annotation',
        'user_id',
        'question_id'
    ];

    public function userQuestionAnswered()
    {
        return $this->belongsTo(UserQuestionAnswered::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\UserQuestionAnnotationFactory::new();
    }
}
