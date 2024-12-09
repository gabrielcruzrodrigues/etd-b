<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Matter\Models\Matter;
use Modules\Question\Models\Question;
use Modules\Simulation\Models\AreaOfKnowledge;
use Modules\Simulation\Models\SimulationAlternative;

class Simulation extends Model
{
    protected $table = 'simulations';

    protected $fillable = ['image_path', 'title', 'question_id', 'area_of_knowledge_id', 'matter_id', 'type'];

    public function areaOfKnowledge()
    {
        return $this->belongsTo(AreaOfKnowledge::class);
    }

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function simulationAlternatives()
    {
        return $this->hasMany(SimulationAlternative::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function simulatedQuestions()
    {
        return $this->belongsToMany(Question::class, 'simulated_questions');
    }

    public function UserSimulationFinished()
    {
        return $this->hasMany(UserSimulationFinished::class);
    }
}
