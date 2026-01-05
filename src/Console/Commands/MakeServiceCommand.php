<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Illuminate\Console\Command;
use Alvarez\Generators\ServiceGenerator;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    // O comando que o usuário digitará no terminal
    protected $signature = 'concrete:service {name : The name of the model}';

    protected $description = 'Create a new Concrete Model Service';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $generator = new ServiceGenerator();

        try {
            $content = $generator->generate($name, 'model-service');

            // $this->laravel->path() retorna o caminho para a pasta 'app' do projeto
            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'Services';
            $path = $basePath . DIRECTORY_SEPARATOR . "{$name}Service.php";

            // Garante que a pasta app/Services existe
            if (!$this->files->isDirectory($basePath)) {
                $this->files->makeDirectory($basePath, 0755, true);
            }

            if ($this->files->exists($path)) {
                $this->error("Service already exists!");
                return;
            }

            $this->files->put($path, $content);

            $this->info("Service created successfully at: app/Services/{$name}Service.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}