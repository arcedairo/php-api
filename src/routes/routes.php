<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

$resource = $_GET['resource'] ?? null;

switch ($resource) {
    case 'user':
        return require_once 'user.routes.php';
    
    default:
        return require_once 'main.routes.php';
}