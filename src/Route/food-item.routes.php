<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu\Route;

use PH7\ApiSimpleMenu\Service\FoodItem;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

enum FoodItemAction: string
{
    case RETRIEVE_ALL = 'retrieveall';
    case RETRIEVE = 'retrieve';

    public function getResponse(): string 
    {  
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody);

        $itemId = $_REQUEST['id'] ?? '';

        $item = new FoodItem();

            $response = match ($this){
                self::RETRIEVE_ALL => $item -> retrieveAll(),
                self::RETRIEVE => $item-> retrieve($itemId),
            };
       
        return json_encode($response);
    }
}

$action = $_REQUEST['action'] ?? null;

$itemAction = FoodItemAction::tryFrom($action);

if($itemAction){
    echo $itemAction-> getResponse();
} else {
    require_once 'main.routes.php';
}