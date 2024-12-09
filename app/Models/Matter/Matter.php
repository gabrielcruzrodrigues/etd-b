<?php

namespace App\Models\Matter;


use App\Models\Content\Content;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Matter extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    
    public function Contents()
    {
        return $this->hasMany(Content::class);
    }

    public function Questions()
    {
        return $this->hasMany(Question::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\MatterFactory::new();
    }
}
