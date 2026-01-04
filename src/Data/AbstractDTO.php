<?php

namespace Daniel\ConcretePhp\Data;

use Daniel\ConcretePhp\Contracts\IsDTO;

/**
 * Abstract Data Transfer Object (DTO)
 *
 * Provides a base structure for data transfer objects with support for
 * serialization, cloning, and filtering properties.
 */
abstract class AbstractDTO implements IsDTO
{
    /**
     * Create a new instance from an associative array.
     *
     * @param array $data The input data to populate the DTO.
     * @return static Returns an instance of the class that extends AbstractDTO.
     */
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    /**
     * Convert the DTO properties to an associative array.
     *
     * @return array An associative array of the DTO properties.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Returns the DTO as an array, excluding specified keys.
     *
     * @param array $keys The property names to be excluded from the resulting array.
     * @return array The filtered array.
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->toArray(), array_flip($keys));
    }

    /**
     * Create a copy of the DTO with modified values.
     *
     * Useful for updating specific properties while maintaining immutability.
     *
     * @param array $values The values to be merged or updated in the new instance.
     * @return static A new instance of the DTO with the updated values.
     */
    public function cloneWith(array $values): static
    {
        return static::fromArray(array_merge($this->toArray(), $values));
    }

    /**
     * Create a new instance from a JSON string.
     *
     * @param string $json A valid JSON string.
     * @return static Returns an instance of the class that extends AbstractDTO.
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);
        return static::fromArray($data);
    }

    /**
     * Convert the DTO to a JSON string.
     *
     * @return string Returns the JSON representation of the DTO.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}