<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Alvarez\ConcretePhp\Generators\ServiceGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select; // Importamos o select

class MakeServiceCommand extends Command
{
    protected $signature = 'concrete:service {name? : The name of the model}';
    protected $description = 'Create a new Concrete Model Service';
    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        UI::displayLogo($this);

        // 1. Pergunta o Nome
        $inputName = $this->argument('name') ?? text(
            label: 'What is the name of the service?',
            placeholder: 'E.g. User or Admin/User',
            required: true
        );

        // 2. Pergunta o Tipo (Select)
        $type = select(
            label: 'What type of service would you like to create?',
            options: [
                'model-service' => 'Model Service (Standard)',
                // Futuros tipos entrarão aqui
            ],
            default: 'model-service'
        );

        $name = preg_replace('/^Service[\/\\\]/i', '', $inputName);
        $generator = new ServiceGenerator();

        try {
            // Pass the selected $type to the generator
            $content = $generator->generate($name, $type);

            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Services';
            $relativeDiskPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
            $path = $basePath . DIRECTORY_SEPARATOR . "{$relativeDiskPath}Service.php";

            $directory = dirname($path);

            if (!$this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true, true);
            }

            if ($this->files->exists($path)) {
                $this->error("Service already exists!");
                return;
            }

            $this->files->put($path, $content);

            $this->info("{$type} created successfully!");
            $this->line("<fg=gray>Location:</> app/Services/{$relativeDiskPath}Service.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}