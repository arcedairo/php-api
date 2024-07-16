<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu\Service;

use PH7\ApiSimpleMenu\Dal\UserDal;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\ApiSimpleMenu\Validation\UserValidation;
use PH7\PhpHttpResponseHeader\Http;
use PH7\JustHttp\StatusCode;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\RedException\SQL;
use Respect\Validation\Validator as v;
use PH7\ApiSimpleMenu\Entity\User as UserEntity;

class User {

    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function create(mixed $data): object
    {
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {

            $userUuid = Uuid::uuid4();

            $userEntity = new UserEntity();
            $userEntity->setUserUuid($userUuid)->setFirstName($data->first)->setLastName($data->last)->setEmail($data->email)->setPhone($data->phone)->setCreationDate(date(self::DATE_TIME_FORMAT));
            
            try{
                UserDal::create($userEntity);
            } catch(SQL $exception){
                Http::SetHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
                $data = array();
            }
            

            return $data;
        }

        throw new InvalidValidationException('Invalid user payload');

        return $this;
    }

    public function retrieveAll(): array
    {
        $users = UserDal::getAll();

        return array_map(function(object $user): object{
            unset($user['id']);
            return $user;
        }, $users);
    }

    public function retrieve(string $userUuid): array 
    { 
        if(v::uuid(version:4)->validate($userUuid)){
            
            if($user = UserDal::get($userUuid)){
                unset($user['id']);
                return $user;
            }
            return []; 
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

    public function remove(object $data): bool
    {
        $userValidation = new UserValidation($data);
        if($userValidation->isRemoveSchemaValid()){
            return UserDal::remove($data->userUuid);
        } 
        
        throw new InvalidValidationException("Invalid user UUID");
    }
}