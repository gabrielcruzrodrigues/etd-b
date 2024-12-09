<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Matter\Models\Matter;
use Modules\Simulation\Models\Simulation;

class AreaOfKnowledge extends Model
{
    protected $table = 'area_of_knowledges';

    protected $fillable = ['name'];

    public function matters()
    {
        return $this->belongsToMany(Matter::class, 'area_of_knowledge_matter');
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }
}
