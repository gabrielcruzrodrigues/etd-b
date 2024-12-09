<?php

namespace Modules\Lesson\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Lesson\Models\Lesson;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'available_plain',
        'release_date',
    ];

    protected $casts = [
        'active' => 'boolean',
        'release_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function Lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
