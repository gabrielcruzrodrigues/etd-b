<?php
namespace App\Models\User;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    public function School()
    {
        // return $this->belongsTo(School::class);
    }
}