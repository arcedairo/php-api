<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use PH7\ApiSimpleMenu\Service\User;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

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

        $userId = $_REQUEST['id'] ?? null;

        $user = new User();
        try {
            $response = match ($this){
                self::CREATE => $user-> create($postBody),
                self::RETRIEVE_ALL => $user -> retrieveAll(),
                self::RETRIEVE => $user-> retrieve($userId),
                self::REMOVE => $user-> remove($postBody),
                self::UPDATE => $user -> update($postBody),
            };
        } catch (InvalidValidationException $e) {
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

$action = $_REQUEST['action'] ?? null;

$userAction = match ($action){
    'create' => UserAction::CREATE,
    'retrieve' => UserAction::RETRIEVE,
    'retrieveall' => UserAction::RETRIEVE_ALL,
    'remove' => UserAction::REMOVE,
    'update' => UserAction::UPDATE,
    default => UserAction::RETRIEVE_ALL,
};

echo $userAction-> getResponse();