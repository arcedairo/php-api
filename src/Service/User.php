<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu\Service;

use Exception;
use Firebase\JWT\JWT;
use PH7\ApiSimpleMenu\Dal\UserDal;
use PH7\ApiSimpleMenu\Service\Exception\CannotLoginUserException;
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

    public function __construct(protected string $jwtSecretKey)
    {
    }


    public function login(mixed $data): array{
        
        $userValidation = new UserValidation($data);
        if($userValidation->isLoginSchemaValid()){
            if(UserDal::doesEmailExist($data->email)) {
                
                $user = UserDal::getByEmail($data->email);
                
                $areCredentialsValid = $user->getEmail() && password_verify($data->password, $user->getPassword());

                if($areCredentialsValid) {
                    $userName = "{$user->getFirstName()} {$user->getLastName()}";
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

                        $this->jwtSecretKey, 
                        $_ENV['JWT_ALGO_ENCRYPTION']
                    );

                    try{
                        UserDal::setToken($jwtToken, $user->getUserUuid());
                    } catch (Exception $e){
                        throw new CannotLoginUserException('Cannot set token to user');
                    }

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
            
            if(!$userUuid = UserDal::create($userEntity)){
                HttpResponse::SetHeadersByCode(StatusCode::BAD_REQUEST);
                $data = array();
            }
            
            HttpResponse::setHeadersByCode(StatusCode::CREATED);

            $data->userUuid = $userUuid;
            
            return $data;
        }

        throw new InvalidValidationException('Invalid user payload');

        return $this;
    }

    public function retrieveAll(): array
    {
        return UserDal::getAll();
    }

    public function retrieve(string $userUuid): array
    { 
        if(v::uuid(version:4)->validate($userUuid)){
            if($user = UserDal::getById($userUuid)){
                
              if($user->getUserUuid()){  
                return [
                    'userUuid' => $user->getUserUuid(),
                    'first' => $user->getFirstName(),
                    'last' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'phone' => $user->getPhone(),
                    'creationDate' => $user->getCreationDate(),
                ];
               }
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
                HttpResponse::setHeadersByCode(StatusCode::NOT_FOUND);
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