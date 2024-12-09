<?php
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\User;

class Archivements extends Model
{
    protected $fillable = [
        'user_id',
        'questions_level',
        'comments_level',
        'flashcard_level',
        'flashcard_created_level',
        'simulated_level',
        'essay_level',
        'study_record_level'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
