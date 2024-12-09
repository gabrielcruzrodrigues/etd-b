<?php
namespace App\Models\Institution;

use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
     use HasFactory;

     protected $fillable = [
          'name'
     ];

     public function Questions()
     {
          $this->hasMany(Question::class);
     }

     protected static function newFactory()
    {
        return \Database\Factories\InstitutionFactory::new();
    }
}