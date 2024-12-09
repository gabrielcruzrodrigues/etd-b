<?php
namespace App\Models\Year;

use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
     use HasFactory;

     protected $fillable = [
          'year'
     ];

     public function Questions()
     {
          $this->hasMany(Question::class);
     }

     protected static function newFactory()
    {
        return \Database\Factories\YearFactory::new();
    }
}