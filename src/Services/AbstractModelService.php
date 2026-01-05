<?php

namespace Alvarez\ConcretePhp\Services;

use Alvarez\ConcretePhp\Contracts\IsDTO;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelService
 * * This abstract class serves as a base for the Service Layer in a Laravel application.
 * It bridges the gap between Data Transfer Objects (DTOs) and Eloquent Models,
 * encapsulating business logic and database interactions to keep controllers clean.
 */
abstract class AbstractModelService
{
    /**
     * The Eloquent Model instance representing the current database record.
     * * @var Model
     */
    protected Model $record;

    /**
     * ModelService constructor.
     * * Initializes the service with a specific Model instance.
     * * @param Model $record The Eloquent model instance to be managed by this service.
     */
    public function __construct(Model $record)
    {
        // Delegates the assignment to the setRecord method to allow for potential override logic.
        $this->setRecord($record);
    }

    /**
     * Create a new database record and return a new service instance.
     * * This static factory method accepts either a raw array or a DTO.
     * It resolves the model class, persists the data, and wraps the result in the service.
     * * @param array|IsDTO $data The data used to create the record (as an associative array or DTO).
     * @return static Returns a new instance of the child Service class.
     */
    public static function create(array|IsDTO $data): static
    {
        // Checks if the provided data is an object implementing the IsDTO interface.
        if ($data instanceof IsDTO) {
            // Converts the DTO to an array, calls create on the resolved model path, 
            // and returns a new static service instance with the created model.
            return new static(static::getModelPath()::create($data->toArray()));
        }

        // If data is a standard array, it directly creates the model and wraps it in the service.
        return new static(static::getModelPath()::create($data));
    }

    /**
     * Find an existing record by its primary key and wrap it in a service instance.
     * * Uses Laravel's findOrFail to ensure the record exists or throw a 404 exception.
     * * @param string|int $id The unique identifier (ID) of the record.
     * @return static Returns a new instance of the child Service class containing the found model.
     */
    public static function find(string|int $id): static
    {
        // Resolves the model class string and executes the findOrFail query.
        return new static(static::getModelPath()::findOrFail($id));
    }

    /**
     * Update the current model record with new data.
     * * Handles both DTOs and arrays. This method allows for method chaining.
     * * @param array|IsDTO $data The updated data set.
     * @return static Returns the current service instance ($this) for chaining.
     */
    public function update(array|IsDTO $data): static
    {
        // If a DTO is provided, convert it to an array before updating the Eloquent model.
        if ($data instanceof IsDTO) {
            // Accesses the internal model and performs the update.
            $this->getRecord()->update($data->toArray());

            // Returns the instance to allow further operations like $service->update($dto)->someOtherMethod().
            return $this;
        }

        // Performs the update using a standard associative array.
        $this->getRecord()->update($data);

        // Ensures the service instance is returned.
        return $this;
    }

    /**
     * Retrieve the underlying Eloquent Model instance.
     * * @return Model The model currently held by the service.
     */
    public function getRecord(): Model
    {
        return $this->record;
    }

    /**
     * Set or replace the Model instance managed by this service.
     * * @param Model $record The new model instance.
     * @return static Returns the service instance for chaining.
     */
    public function setRecord(Model $record): static
    {
        // Assigns the model to the protected property.
        $this->record = $record;

        // Returns $this to support fluent interface usage.
        return $this;
    }

    /**
     * Resolve the full class path of the associated Eloquent Model.
     * * By convention, this method assumes the Service is named {ModelName}Service
     * and the Model is located in the 'App\Models' namespace.
     * Example: 'UserService' resolves to 'App\Models\User'.
     * * @return string The fully qualified class name of the Model.
     */
    public static function getModelPath(): string
    {
        // Extracts the short class name (e.g., "UserService") without the namespace.
        $serviceClassName = class_basename(static::class);

        // Removes the "Service" suffix from the name to isolate the Model name.
        $modelName = str_replace('Service', '', $serviceClassName);

        // Concatenates the default Laravel model namespace with the isolated name.
        return 'App\\Models\\' . $modelName;
    }
}