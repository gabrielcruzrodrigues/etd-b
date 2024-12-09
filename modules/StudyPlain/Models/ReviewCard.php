<?php

namespace Modules\StudyPlain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewCard extends Model
{
    use HasFactory;

    protected $table = 'review_card';

    protected $fillable = [
        'date',
        'completed',
        'study_plain_id',
    ];

    public function StudyPlain()
    {
        return $this->belongsTo(StudyPlain::class);
    }
}
