<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use Exception;
use PH7\ApiSimpleMenu\Exception\InvalidValidationException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

require_once dirname(__DIR__) .'/endpoints/User.php'; 

enum UserAction: string
{
    case CREATE = 'create';
    case RETRIEVE_ALL = 'retrieveAll';
    case RETRIEVE = 'retrieve';
    case REMOVE = 'remove';
    case UPDATE = 'update';

    public function getResponse(): string 
    {  
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody);

        $userId = !empty($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

        $user = new User('Dairo', 'arcedairo@unicauca.edu.co','3165500424');
        try {
            $response = match ($this){
                self::CREATE => $user-> create($postBody),
                self::RETRIEVE_ALL => $user -> retrieveAll(),
                self::RETRIEVE => $user-> retrieve($userId),
                self::REMOVE => $user-> remove($userId),
                self::UPDATE => $user -> update($postBody),
            };
        } catch (InvalidValidationException | Exception $e) {
            Http::setHeadersByCode(StatusCode::BAD_REQUEST);
            $response = [
                'errors' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ];
        }

        return json_encode($response);
    }
}

$action = $_GET['action'] ?? null;

$userAction = match ($action){
    'create' => UserAction::CREATE,
    'retrieve' => UserAction::RETRIEVE,
    'remove' => UserAction::REMOVE,
    'update' => UserAction::UPDATE,
    default => UserAction::RETRIEVE_ALL,
};

echo $userAction-> getResponse();