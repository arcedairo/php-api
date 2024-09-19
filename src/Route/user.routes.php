<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu\Route;

use PH7\ApiSimpleMenu\Route\Exception\NotFoundException;
use PH7\ApiSimpleMenu\Service\User;
use PH7\ApiSimpleMenu\service\Exception\EmailExistsException;
use PH7\ApiSimpleMenu\Service\SecretKey;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http as HttpResponse;

enum UserAction: string
{
    case CREATE = 'create';
    case LOGIN = 'login';
    case RETRIEVE_ALL = 'retrieveall';
    case RETRIEVE = 'retrieve';
    case REMOVE = 'remove';
    case UPDATE = 'update';

    /**
     * @throws Exception\NotFoundException
     */

    public function getResponse(): string 
    {  
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody);

        $userId = $_REQUEST['id'] ?? '';

        $jwtToken = SecretKey::getJwtSecretKey();

        $user = new User($jwtToken);
        
        try {
            $expectHttpMethod = match($this){
                self::LOGIN => Http::POST_METHOD,
                self::CREATE => Http::POST_METHOD,
                self::UPDATE => Http::POST_METHOD,
                self::RETRIEVE_ALL => Http::GET_METHOD,
                self::RETRIEVE => Http::GET_METHOD,
                self::REMOVE => Http::DELETE_METHOD,
            };

            if(Http::doesHttpMethodMatch($expectHttpMethod) === false){
                throw new NotFoundException('Http method is incorrect. Request not found');
            }

            $response = match ($this){
                self::LOGIN => $user->login($postBody),
                self::CREATE => $user-> create($postBody),
                self::UPDATE => $user -> update($postBody),
                self::RETRIEVE_ALL => $user -> retrieveAll(),
                self::RETRIEVE => $user-> retrieve($userId),
                self::REMOVE => $user-> remove($postBody),
            };
        } catch (InvalidValidationException $e) {
            HttpResponse::setHeadersByCode(StatusCode::BAD_REQUEST);
            $response = [
                'errors' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ];
        } catch (EmailExistsException $e){
            HttpResponse::setHeadersByCode(StatusCode::BAD_REQUEST);
            $response = [
                'errors' => [
                    'message' => $e->getMessage()
                ]
                ];
        }

        return json_encode($response);
    }
}

$action = $_REQUEST['action'] ?? null;

$userAction = UserAction::tryFrom($action);

if($userAction){
    echo $userAction-> getResponse();
} else {
    require_once 'main.routes.php';
}