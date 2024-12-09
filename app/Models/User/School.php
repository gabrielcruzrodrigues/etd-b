<?php
namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\User;

class School extends Model
{
    public function Classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function Users()
    {
        return $this->hasMany(User::class);
    }
}