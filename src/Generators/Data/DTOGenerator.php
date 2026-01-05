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

        // Explode the name if it comes with slashes (User/CreateUser)
        $pathParts = explode('/', str_replace('\\', '/', $name));
        $className = array_pop($pathParts); // Get only 'CreateUser'

        // Build the dynamic namespace
        $subNamespace = !empty($pathParts) ? '\\' . implode('\\', $pathParts) : '';
        $fullNamespace = 'App\\DTO' . $subNamespace;

        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$fullNamespace, "{$className}DTO"],
            $stub
        );
    }
}