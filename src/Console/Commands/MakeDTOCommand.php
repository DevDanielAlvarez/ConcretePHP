<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Alvarez\Generators\DtoGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\text;

class MakeDTOCommand extends Command
{
    /**
     * A assinatura do comando no terminal.
     */
    protected $signature = 'concrete:dto {name? : The name of the DTO}';

    /**
     * A descrição que aparece no php artisan list.
     */
    protected $description = 'Create a new Concrete Data Transfer Object (DTO)';

    protected Filesystem $files;

    /**
     * O Filesystem é injetado pelo Laravel automaticamente.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        UI::displayLogo($this);

        // 1. Pergunta o nome se não foi passado como argumento
        $name = $this->argument('name') ?? text(
            label: 'What is the name of the DTO?',
            placeholder: 'E.g. CreateUser, UpdateTask',
            required: true
        );

        // 2. Instancia o seu novo gerador especializado
        $generator = new DtoGenerator();

        try {
            // Gera o conteúdo usando o stub de DTO
            $content = $generator->generate($name, 'dto');

            // Define o caminho final: app/Data/{Name}Data.php
            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Data';
            $path = $basePath . DIRECTORY_SEPARATOR . "{$name}Data.php";

            // Cria a pasta Data se ela não existir
            if (!$this->files->isDirectory($basePath)) {
                $this->files->makeDirectory($basePath, 0755, true);
            }

            // Evita sobrescrever arquivos existentes
            if ($this->files->exists($path)) {
                $this->error("DTO [{$name}Data] already exists!");
                return;
            }

            // Salva o arquivo
            $this->files->put($path, $content);

            $this->info("DTO created successfully!");
            $this->line("<fg=gray>Location:</> app/Data/{$name}Data.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}