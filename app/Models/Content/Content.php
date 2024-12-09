<?php
namespace App\Models\Content;


use App\Models\Matter\Topic;
use App\Models\Matter\Matter;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Content extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'matter_id'];

    public function Matter()
    {
        return $this->belongsTo(Matter::class);
    }

    public function Topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function Questions()
    {
        return $this->hasMany(Question::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\ContentFactory::new();
    }
}
