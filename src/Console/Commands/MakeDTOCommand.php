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

        // Remove "DTO/" ou "DTO\" do início da string caso o usuário tenha digitado
        $name = preg_replace('/^DTO[\/\\\]/i', '', $inputName);

        $generator = new DTOGenerator();

        try {
            $content = $generator->generate($name, 'dto');

            // Caminho base fixo em app/DTO
            $basePath = $this->laravel->path() . DIRECTORY_SEPARATOR . 'DTO';

            // Monta o caminho final garantindo que as barras estejam corretas para o SO
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