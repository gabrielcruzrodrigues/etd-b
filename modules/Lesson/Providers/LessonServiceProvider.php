<?php

namespace Modules\Lesson\Providers;

use Illuminate\Support\ServiceProvider;

class LessonServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }
}
