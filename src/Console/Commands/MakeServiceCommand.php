<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Illuminate\Console\Command;
use Alvarez\Generators\ServiceGenerator;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\text; // Importando o Prompt

class MakeServiceCommand extends Command
{
    // Tornamos o 'name' opcional na assinatura
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
        // Se o nome não for passado via terminal, abrimos um prompt bonito
        $name = $this->argument('name') ?? text(
            label: 'What is the name of the model?',
            placeholder: 'E.g. User, Task, Order',
            required: true
        );

        $generator = new ServiceGenerator();

        try {
            $content = $generator->generate($name, 'model-service');

            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Services';
            $path = $basePath . DIRECTORY_SEPARATOR . "{$name}Service.php";

            if (!$this->files->isDirectory($basePath)) {
                $this->files->makeDirectory($basePath, 0755, true);
            }

            if ($this->files->exists($path)) {
                $this->error("Service [{$name}Service] already exists!");
                return;
            }

            $this->files->put($path, $content);
            $this->info("Service created successfully at: app/Services/{$name}Service.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}