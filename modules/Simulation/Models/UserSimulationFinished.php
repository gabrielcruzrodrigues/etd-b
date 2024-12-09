<?php
namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Simulation\Models\Simulation;
use Modules\User\Models\User;

class UserSimulationFinished extends Model
{
    protected $fillable = ['user_id', 'simulation_id'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function Simulation()
    {
        return $this->belongsTo(Simulation::class);
    }
}
