<?php

use Alvarez\ConcretePhp\Services\AbstractModelService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * 1. Independent Eloquent Setup
 * Initializes an in-memory database so Eloquent can function
 * without requiring the full Laravel framework or Orchestra Testbench.
 */
beforeAll(function () {
    $capsule = new Capsule;
    $capsule->addConnection([
        'driver' => 'sqlite',
        'database' => ':memory:',
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    // Create the necessary table for testing purposes
    Capsule::schema()->create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });
});

/**
 * 2. Support Classes
 * Mock Model and Service to test the abstract logic.
 */
class Task extends Model
{
    protected $fillable = ['title'];
}

class TaskService extends AbstractModelService
{
    /**
     * Override the path to point to our local Task mock.
     */
    public static function getModelPath(): string
    {
        return Task::class;
    }
}

/**
 * 3. Feature Tests
 */

test('it can create a record and return a service instance', function () {
    // Arrange
    $data = ['title' => 'Make tea'];

    // Act
    $service = TaskService::create($data);

    // Assert
    expect($service->getRecord())->toBeInstanceOf(Task::class);
    expect($service->getRecord()->title)->toBe('Make tea');

    // Verify persistence in the database
    expect(Task::where('title', 'Make tea')->exists())->toBeTrue();
});

test('it can find an existing record and wrap it in a service', function () {
    // Arrange: Manually create a record in the DB
    $task = Task::create(['title' => 'Existing Task']);

    // Act: Use the service to find it
    $service = TaskService::find($task->id);

    // Assert
    expect($service->getRecord())->toBeInstanceOf(Task::class);
    expect($service->getRecord()->id)->toBe($task->id);
    expect($service->getRecord()->title)->toBe('Existing Task');
});

test('it can update a record through the service', function () {
    // Arrange
    $service = TaskService::create(['title' => 'Old Title']);

    // Act
    $service->update(['title' => 'New Title']);

    // Assert
    expect($service->getRecord()->title)->toBe('New Title');

    // Refresh from DB to verify persistence
    $dbTask = Task::find($service->getRecord()->id);
    expect($dbTask->title)->toBe('New Title');
});

test('it can set and get a record instance manually', function () {
    // Arrange
    $task = new Task(['title' => 'Manual Task']);
    $service = new TaskService($task);

    // Act & Assert
    expect($service->getRecord())->toBe($task);

    $newTask = new Task(['title' => 'Another Task']);
    $service->setRecord($newTask);

    expect($service->getRecord())->toBe($newTask);
});