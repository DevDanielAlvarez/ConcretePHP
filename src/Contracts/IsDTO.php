<?php

namespace Alvarez\ConcretePhp\Contracts;

interface IsDTO
{
    public function toArray(): array;

    public static function fromArray(array $data): static;

    public function toJson(): string;

    public static function fromJson(string $json): self;
}