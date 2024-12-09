<?php

namespace Modules\Simulation\Providers;

use Illuminate\Support\ServiceProvider;

class SimulationServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }
}
