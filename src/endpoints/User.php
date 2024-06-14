<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\ApiSimpleMenu\Validation\UserValidation;
use Respect\Validation\Validator as v;

class User {

    public readonly ?string $userId;

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
        
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {
            return $data;
        }

        throw new InvalidValidationException('Invalid user payload');

        return $this;
    }

    public function retrieveAll(): array
    {
        return [];
    }

    public function retrieve(string $userId): self 
    { 
        if(v::uuid(version:4)->validate($userId)){
            $this->userId = $userId;

            return $this; 
        } 
        
        throw new InvalidValidationException("Invalid user UUID");
    }

    public function update(mixed $postBody): object
    {   
        $userValidation = new UserValidation($postBody);
        if ($userValidation->isUpdateSchemaValid()) {
            return $postBody;
        }
        
        throw new InvalidValidationException('Invalid user payload');
    }

    public function remove(string $userId): bool
    {
        if(v::uuid(version:4)->validate($userId)){
            $this->userId = $userId;
        } else {
            throw new InvalidValidationException("Invalid user UUID");
        }

        return true;
    }
}