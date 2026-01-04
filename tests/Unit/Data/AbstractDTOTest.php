<?php

use Daniel\ConcretePhp\Data\AbstractDTO;

/**
 * 1. Define a concrete class for testing purposes.
 */
class UserDto extends AbstractDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {}
}

/**
 * 2. Execute tests using specific data for Daniel Alvarez.
 */
test('it can create a DTO from an array and convert back', function () {
    $data = [
        'id' => 123,
        'name' => 'Daniel Alvarez',
        'email' => 'daniel@alvarez.com'
    ];

    // Test fromArray (uses the splat operator ...$data internally)
    $dto = UserDto::fromArray($data);

    expect($dto->name)->toBe('Daniel Alvarez')
        ->and($dto->email)->toBe('daniel@alvarez.com')
        ->and($dto->toArray())->toBe($data);
});

test('it can handle JSON serialization', function () {
    $json = '{"id":1,"name":"Daniel Alvarez","email":"daniel@alvarez.com"}';

    // Test fromJson static method
    $dto = UserDto::fromJson($json);

    expect($dto->name)->toBe('Daniel Alvarez')
        ->and($dto->email)->toBe('daniel@alvarez.com')
        ->and($dto->toJson())->toBe($json);
});

test('it can clone with modified values', function () {
    $dto = new UserDto(1, 'Daniel Alvarez', 'daniel@alvarez.com');

    // Changing only the email via cloneWith
    $newDto = $dto->cloneWith(['email' => 'new@email.com']);

    expect($newDto->email)->toBe('new@email.com')
        ->and($newDto->name)->toBe('Daniel Alvarez') // Name remains the same
        ->and($dto->email)->toBe('daniel@alvarez.com'); // Original remains intact (Immutability)
});

test('it can exclude keys using except', function () {
    $dto = new UserDto(1, 'Daniel Alvarez', 'daniel@alvarez.com');
    
    $filtered = $dto->except(['id', 'email']);

    expect($filtered)->toBe(['name' => 'Daniel Alvarez'])
        ->and($filtered)->not->toHaveKey('id');
});