<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Alvarez\ConcretePhp\Generators\ServiceGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\text;

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

        $inputName = $this->argument('name') ?? text(
            label: 'What is the name of the model?',
            placeholder: 'E.g. User or Admin/User',
            required: true
        );

        // Limpa "Service/" do início caso o usuário digite por engano
        $name = preg_replace('/^Service[\/\\\]/i', '', $inputName);

        $type = 'model-service';
        $generator = new ServiceGenerator();

        try {
            $content = $generator->generate($name, $type);

            // Caminho base em app/Services
            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Services';

            // Corrige as barras para o sistema operacional (Windows vs Linux)
            $relativeDiskPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
            $path = $basePath . DIRECTORY_SEPARATOR . "{$relativeDiskPath}Service.php";

            // Obtém a pasta onde o arquivo ficará (ex: app/Services/Auth)
            $directory = dirname($path);

            // Cria as pastas recursivamente se não existirem
            if (!$this->files->exists($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }

            if ($this->files->exists($path)) {
                $this->error("Service already exists at {$path}!");
                return;
            }

            $this->files->put($path, $content);

            $this->info("Model Service created successfully!");
            $this->line("<fg=gray>Location:</> app/Services/{$relativeDiskPath}Service.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}