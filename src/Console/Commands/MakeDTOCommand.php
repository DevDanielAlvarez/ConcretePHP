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

        $inputName = $this->argument('name') ?? text(
            label: 'What is the name of the DTO?',
            placeholder: 'E.g. User/CreateUser',
            required: true
        );

        // Remove "DTO/" or "DTO\" from the beginning of the string if the user typed it
        $name = preg_replace('/^DTO[\/\\\]/i', '', $inputName);

        $generator = new DTOGenerator();

        try {
            $content = $generator->generate($name, 'dto');

            // Fixed base path at app/DTO
            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'DTO';

            // Build the final path ensuring that slashes are correct for the OS
            $relativeDiskPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
            $path = $basePath . DIRECTORY_SEPARATOR . "{$relativeDiskPath}DTO.php";

            // Obtém o diretório pai do arquivo final
            $directory = dirname($path);

            // Cria o diretório de forma recursiva (0755, true)
            if (!$this->files->exists($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }

            if ($this->files->exists($path)) {
                $this->error("DTO [{$name}DTO] already exists!");
                return;
            }

            $this->files->put($path, $content);

            $this->info("DTO created successfully!");
            $this->line("<fg=gray>Location:</> app/DTO/{$relativeDiskPath}DTO.php");

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}