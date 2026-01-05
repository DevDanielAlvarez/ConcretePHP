<?php

namespace Alvarez\ConcretePhp;

use Alvarez\ConcretePhp\Console\Commands\MakeDTOCommand;
use Illuminate\Support\ServiceProvider;
use Alvarez\ConcretePhp\Console\Commands\MakeServiceCommand;

class ConcreteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Only register commands if running in console (CLI)
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeServiceCommand::class,
                MakeDTOCommand::class
            ]);
        }
    }
}