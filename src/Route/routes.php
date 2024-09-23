<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use PH7\ApiSimpleMenu\Route\Exception\NotFoundException;
use PH7\ApiSimpleMenu\service\Exception\CredentialsInvalidException;
use PH7\ApiSimpleMenu\Validation\Exception\InvalidValidationException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http as HttpResponse;

$resource = $_REQUEST['resource'] ?? null;

try{
return match ($resource) {
    'user' => require_once 'user.routes.php',
    'item' => require_once 'food-item.routes.php',
    default => require_once 'not-found.routes.php',
};
} catch(CredentialsInvalidException $e){
    response([
        'errors' => [
            'message' => $e->getMessage()
        ]
    ]);
} catch (InvalidValidationException $e) {
    HttpResponse::setHeadersByCode(StatusCode::BAD_REQUEST);
    response([
        'errors' => [
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]
    ]);
} catch(NotFoundException $e){
    return require_once 'not-found.routes.php';
} 