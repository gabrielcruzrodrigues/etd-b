<?php

namespace Modules\Essay\Providers;

use Illuminate\Support\ServiceProvider;

class EssayServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }
}
