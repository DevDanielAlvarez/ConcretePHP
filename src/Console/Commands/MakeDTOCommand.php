<?php

namespace Alvarez\ConcretePhp\Console\Commands;

use Alvarez\ConcretePhp\Generators\Data\DTOGenerator;
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

        $name = $this->argument('name') ?? text(
            label: 'What is the name of the DTO?',
            placeholder: 'E.g. User/CreateUser',
            required: true
        );

        $generator = new DTOGenerator();

        try {
            $content = $generator->generate($name, 'dto');

            // Caminho base alterado para 'DTO' conforme seu pedido
            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'DTO';
            $path = $basePath . DIRECTORY_SEPARATOR . "{$name}DTO.php";

            // Pega o diretório completo do arquivo (ex: app/DTO/User/CreateUser)
            $directory = dirname($path);

            // Cria todas as pastas recursivamente
            if (!$this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }

            if ($this->files->exists($path)) {
                $this->error("DTO already exists!");
                return;
            }

            $this->files->put($path, $content);

            $this->info("DTO created successfully!");
            $this->line("<fg=gray>Location:</> app/DTO/{$name}DTO.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}