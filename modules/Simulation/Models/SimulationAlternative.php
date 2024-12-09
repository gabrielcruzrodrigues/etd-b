<?php
namespace Modules\Simulation\Models;

use Modules\Simulation\Models\Simulation;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;
use Modules\Question\Models\Question;

class SimulationAlternative extends Model
{
    protected $table = 'simulation_alternatives';

    protected $fillable = ['simulation_id', 'question_id', 'alternative', 'user_id'];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
