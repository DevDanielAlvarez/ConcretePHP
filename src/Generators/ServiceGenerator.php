<?php

namespace Alvarez\Generators;

/**
 * Class ServiceGenerator
 * Responsible for generating the file content for Model Services
 * based on predefined stubs.
 */
class ServiceGenerator
{
    protected string $name;
    protected string $type;

    /**
     * Entry point to generate the service content.
     * * @param string $name The base name (e.g., 'User')
     * @param string $type The stub type (e.g., 'model-service')
     * @return string The populated stub content
     */
    public function generate(string $name, string $type): string
    {
        $this->name = $name;
        $this->type = $type;

        return $this->resolveStub();
    }

    /**
     * Resolves the stub by getting content and replacing placeholders.
     */
    protected function resolveStub(): string
    {
        $stub = $this->getStubContent();
        return $this->replaceStubContents($stub);
    }

    /**
     * Loads the raw content from the stub file.
     */
    protected function getStubContent(): string
    {
        $path = __DIR__ . "/Stubs/model.service.stub";

        if (!file_exists($path)) {
            throw new \Exception("Stub file for '{$this->type}' not found at: {$path}");
        }

        return file_get_contents($path);
    }

    /**
     * Replaces placeholders with actual class, namespace, and model names.
     */
    protected function replaceStubContents(string $stub): string
    {
        $replacements = $this->getReplacementsBasedOnType();
        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }

    /**
     * Determines which replacements to use based on the generator type.
     */
    protected function getReplacementsBasedOnType(): array
    {
        return match ($this->type) {
            'model-service' => $this->getModelServiceReplacements(),
            default => throw new \Exception("Replacements for type {$this->type} not configured."),
        };
    }

    /**
     * Logic specifically for Model Services.
     */
    protected function getModelServiceReplacements(): array
    {
        // Convention: App\Services namespace
        $namespace = "App\\Services";

        // If $name is 'User', class becomes 'UserService'
        $class = $this->name . "Service";

        // The model it points to is 'User'
        $model = $this->name;

        return [
            '{{ namespace }}' => $namespace,
            '{{ class }}' => $class,
            '{{ model }}' => $model,
        ];
    }
}