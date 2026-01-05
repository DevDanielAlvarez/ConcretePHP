<?php

namespace Alvarez\ConcretePhp\Generators\Data;

class DTOGenerator
{
    public function generate(string $name, string $type): string
    {
        $stubPath = __DIR__ . '/../Stubs/Data/dto.stub';

        if (!file_exists($stubPath)) {
            throw new \Exception("Stub not found at: {$stubPath}");
        }

        $stub = file_get_contents($stubPath);

        // Explode o nome caso venha com barras (User/CreateUser)
        $pathParts = explode('/', str_replace('\\', '/', $name));
        $className = array_pop($pathParts); // Pega apenas 'CreateUser'

        // Constrói o namespace dinâmico
        $subNamespace = !empty($pathParts) ? '\\' . implode('\\', $pathParts) : '';
        $fullNamespace = 'App\\DTO' . $subNamespace;

        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$fullNamespace, "{$className}DTO"],
            $stub
        );
    }
}