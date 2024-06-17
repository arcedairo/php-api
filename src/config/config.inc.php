<?php
namespace PH7\ApiSimpleMenu;

use Dotenv\Dotenv;

$path = dirname(__DIR__,2);
$dotenv = Dotenv::createImmutable($path);
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);