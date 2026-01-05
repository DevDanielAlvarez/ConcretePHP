<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Alvarez\Generators\DtoGenerator;
use Illuminate\Console\Command;
use Alvarez\Generators\ServiceGenerator; // Ou DtoGenerator se você separar as classes
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\text;

class MakeDTOCommand extends Command
{
    // ... signature e properties

    public function handle()
    {
        UI::displayLogo($this);

        $name = $this->argument('name') ?? text(
            label: 'What is the name of the DTO?',
            placeholder: 'E.g. CreateUser, UpdateTask',
            required: true
        );

        // Instanciando o DtoGenerator em vez do ServiceGenerator
        $generator = new DtoGenerator();

        try {
            // O tipo 'dto' agora é processado pelo gerador especializado
            $content = $generator->generate($name, 'dto');

            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Data';
            $path = $basePath . DIRECTORY_SEPARATOR . "{$name}Data.php";

            // ... lógica de criação de diretório e salvamento (mesma do Service)
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}