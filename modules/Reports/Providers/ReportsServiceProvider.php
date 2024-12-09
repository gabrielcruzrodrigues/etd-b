<?php

namespace Modules\Reports\Providers;

use Illuminate\Support\ServiceProvider;

class ReportsServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }
}
