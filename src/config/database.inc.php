<?php
namespace PH7\ApiSimpleMenu;
use RedBeanPHP\R;

$dsn = sprintf('mysql:host=%s;dbname=%s', $_ENV['DB_HOST'], $_ENV['DB_NAME']);
R::setup($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'] );

//$environmentEnun = $_ENV['ENVIRONMENT'] === Environment::DEVELOPMENT ? Environment::DEVELOPMENT : Environment::PRODUCTION; 

$currentEnvironment = Environment::tryFrom($_ENV['ENVIRONMENT']);
//var_dump($currentEnvironment?->environmentName() === Environment::PRODUCTION->value);
if($currentEnvironment?->environmentName() !== Environment::DEVELOPMENT->value){
  echo 'RedBean frozen';
  R::freeze(true);
}

