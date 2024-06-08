<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use PH7\ApiSimpleMenu\Exception\InvalidValidationException;
use Respect\Validation\Validator as v;

class User {

    public readonly int $userId;

    public function __construct(
        public readonly string $name, 
        public readonly string $email, 
        public readonly string $phone)
    {
    }

    public function create(mixed $data): object
    {
        $minimumLength = 2;
        $maximumLength = 60;

        $schemaValidation = v::attribute('first', v::stringType()->length($minimumLength,$maximumLength))
            ->attribute('last', v::stringType()->length($minimumLength, $maximumLength))
            ->attribute('email', v::email(), mandatory:false)
            ->attribute('phone', v::phone(), mandatory:false);

        if ($schemaValidation->validate($data)) {
            return $data;
        }

        throw new InvalidValidationException('Invalid Data');

        return $this;
    }

    public function retrieveAll(): array
    {
        return [];
    }

    public function retrieve(string $userId): self 
    {
        $this-> userId = $userId;
        return $this;
    }

    public function update(mixed $postBody): self
    {
        return $this;
    }

    public function remove(string $userId): bool
    {
        return true;
    }
}