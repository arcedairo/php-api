<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

$resource = $_REQUEST['resource'] ?? null;

return match ($resource) {
    'user' => require_once 'user.routes.php',
    'item' => require_once 'food-item.routes.php',
    default => require_once 'main.routes.php',
};