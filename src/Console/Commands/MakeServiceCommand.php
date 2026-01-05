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

            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Services';
            $relativeDiskPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
            $path = $basePath . DIRECTORY_SEPARATOR . "{$relativeDiskPath}Service.php";

            $directory = dirname($path);

            // Forçamos a criação usando a instância do Filesystem de forma mais robusta
            if (!$this->files->isDirectory($directory)) {
                // O terceiro parâmetro 'true' habilita o modo recursivo (mkdir -p)
                $this->files->makeDirectory($directory, 0755, true, true);
            }

            if ($this->files->exists($path)) {
                $this->error("Service already exists!");
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