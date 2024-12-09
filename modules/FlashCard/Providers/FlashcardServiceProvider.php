<?php

namespace Modules\FlashCard\Providers;

use Illuminate\Support\ServiceProvider;

class FlashcardServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }
}
