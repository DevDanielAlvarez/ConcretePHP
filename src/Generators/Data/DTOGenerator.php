<?php

namespace Alvarez\ConcretePhp\Generators\Data;

class DtoGenerator
{
    public function generate(string $name, string $type): string
    {
        // Define o caminho do stub específico para DTO
        $stubPath = __DIR__ . '/Stubs/Data/dto.stub';

        if (!file_exists($stubPath)) {
            throw new \Exception("Stub not found at: {$stubPath}");
        }

        $stub = file_get_contents($stubPath);

        // Substituições
        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ['App\\Data', "{$name}Data"], // Namespace padrão onde o arquivo será salvo
            $stub
        );
    }
}