<?php

namespace Modules\StudyPlain\Providers;

use Illuminate\Support\ServiceProvider;

class StudyPlainServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }
}
