<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu\Service;

use Firebase\JWT\JWT;
use PH7\ApiSimpleMenu\Dal\UserDal;
use PH7\ApiSimpleMenu\service\Exception\EmailExistsException;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\ApiSimpleMenu\Service\Exception\CredentialsInvalidException;
use PH7\ApiSimpleMenu\Validation\UserValidation;
use PH7\PhpHttpResponseHeader\Http as HttpResponse;
use PH7\JustHttp\StatusCode;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\RedException\SQL;
use Respect\Validation\Validator as v;
use PH7\ApiSimpleMenu\Entity\User as UserEntity;

class User {

    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function login(mixed $data): array{
        
        $userValidation = new UserValidation($data);
        if($userValidation->isLoginSchemaValid()){
            if(UserDal::doesEmailExist($data->email)) {
                
                $user = UserDal::getByEmail($data->email);
                
                if($user && password_verify($data->password, $user['password'])) {
                    $userName = "{$user['first_name']} {$user['last_name']}";
                    $currentTime = time();
                    $jwtToken = JWT::encode(
                        [
                            'iss' => $_ENV['APP_URL'],
                            'iat' => $currentTime,
                            'exp' => $currentTime + $_ENV['JWT_TOKEN_EXPIRATION'],
                            'data' => [
                                'email' => $data->email,
                                'name' => $userName
                            ]
                        ],

                        $_ENV['JWT_KEY'], 
                        $_ENV['JWT_ALGO_ENCRYPTION']
                    );

                    return [
                        'token' => $jwtToken,
                        'message' => sprintf('%s successfully logged in!', $userName)
                    ];
                }
            }
            throw new CredentialsInvalidException('Invalid credentials');
        }
        throw new InvalidValidationException('Invalid payload');
    }

    public function create(mixed $data): array|object
    {  
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {

            $userUuid = Uuid::uuid4()->toString();

            $userEntity = new UserEntity();
            $userEntity->setUserUuid($userUuid)->setFirstName($data->first)->setLastName($data->last)->setEmail($data->email)->setPhone($data->phone)->setPassword(hashPassword($data->password))->setCreationDate(date(self::DATE_TIME_FORMAT));
            
            $email = $userEntity->getEmail();
            if(UserDal::doesEmailExist($email)){
                throw new EmailExistsException(sprintf('email address %s already exists', $email));
            }
            
            if(UserDal::create($userEntity) === false){
                HttpResponse::SetHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
                $data = array();
            }
            
            HttpResponse::setHeadersByCode(StatusCode::CREATED);
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
            
            if($user = UserDal::getById($userUuid)){
                unset($user['id']);
                return $user;
            }
            return []; 
        } 
        
        throw new InvalidValidationException("Invalid user UUID");
    }

    public function update(mixed $postBody): array|object
    {   
        $userValidation = new UserValidation($postBody);
        if ($userValidation->isUpdateSchemaValid()) {
            $userUuid = $postBody->userUuid;
            $userEntity = new UserEntity();
            if(!empty($postBody->first)){
                $userEntity->setFirstName($postBody->first);
            }
            if(!empty($postBody->last)){
                $userEntity->setLastName($postBody->last);
            }
            if(!empty($postBody->phone)){
                $userEntity->setPhone($postBody->phone);
            }

            if(UserDal::update($userUuid, $userEntity) === false){
                HttpResponse::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
                return [];
            }

            HttpResponse::setHeadersByCode(StatusCode::OK);
            return $postBody;
        }
        
        throw new InvalidValidationException('Invalid user payload');
    }

    public function remove(mixed $data): bool
    {
        $userValidation = new UserValidation($data);
        if($userValidation->isRemoveSchemaValid()){
            //Http::setHeadersByCode(StatusCode::NO_CONTENT);
            return UserDal::remove($data->userUuid);
        } 
        
        throw new InvalidValidationException("Invalid user UUID");
    }
}