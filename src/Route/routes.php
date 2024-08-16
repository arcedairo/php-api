<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use PH7\ApiSimpleMenu\Route\Exception\NotFoundException;
use PH7\ApiSimpleMenu\service\Exception\CredentialsInvalidException;

$resource = $_REQUEST['resource'] ?? null;

try{
return match ($resource) {
    'user' => require_once 'user.routes.php',
    'item' => require_once 'food-item.routes.php',
    default => require_once 'not-found.routes.php',
};
} catch(CredentialsInvalidException $e){
    response([
        'message' => $e->getMessage()
    ]);
} catch(NotFoundException $e){
    return require_once 'not-found.routes.php';
} 