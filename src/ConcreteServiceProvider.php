<?php

namespace Alvarez\ConcretePhp;

use Illuminate\Support\ServiceProvider;
use Alvarez\ConcretePhp\Console\Commands\MakeServiceCommand;

class ConcreteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Só registra os comandos se estiver rodando via terminal (CLI)
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeServiceCommand::class,
            ]);
        }
    }
}