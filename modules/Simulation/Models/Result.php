<?php
namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Simulation\Models\Simulation;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = ['qty_hits', 'qty_errors', 'conclusion_time', 'simulation_id'];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }
}
