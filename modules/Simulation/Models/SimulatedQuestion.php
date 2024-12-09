<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Simulation\Models\Simulation;
use Modules\Question\Models\Question;

class SimulatedQuestion extends Model
{
    protected $table = 'simulated_questions';

    protected $fillable = ['simulation_id', 'question_id'];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
