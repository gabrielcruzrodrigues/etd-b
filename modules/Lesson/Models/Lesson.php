<?php

namespace Modules\Lesson\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Lesson\Models\File;
use Modules\Lesson\Models\LessonComment;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link_lesson',
        'thumbnail_image_link',
        'banner_image_link',
        'slug',
        'active',
    ];

    public function Files()
    {
        return $this->hasMany(File::class);
    }

    public function Comments()
    {
        return $this->hasMany(LessonComment::class);
    }

    public function Theme()
    {
        return $this->belongsTo(Theme::class);
    }

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
    ];
}
