<?php

namespace Modules\FlashCard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class UserFlashCreated extends Model
{
    protected $fillable = [
        'user_id',
        'flashcard_id'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function FlashCard()
    {
        return $this->belongsTo(FlashCard::class);
    }
}
