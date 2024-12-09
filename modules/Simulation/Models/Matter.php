<?php
namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Simulation\Models\Simulation;
use Modules\Simulation\Models\AreaOfKnowledge;

class Matter extends Model
{
    protected $table = 'matters';

    protected $fillable = ['name'];

    public function areaOfKnowledges()
    {
        return $this->belongsToMany(AreaOfKnowledge::class, 'area_of_knowledge_matter');
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }
}
