<?php

namespace Modules\Lesson\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function Lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
