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
        // 1. Pergunta o Nome (se não enviado via argumento)
        $name = $this->argument('name') ?? text(
            label: 'What is the name of the model?',
            placeholder: 'E.g. User, Task, Order',
            required: true
        );

        // Definimos o tipo fixo como 'model-service' por enquanto
        $type = 'model-service';

        $generator = new ServiceGenerator();

        try {
            $content = $generator->generate($name, $type);

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

            $this->info("Model Service created successfully!");
            $this->info("Location: app/Services/{$name}Service.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}