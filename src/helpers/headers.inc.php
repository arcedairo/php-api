<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;
use PH7\PhpHttpResponseHeader\Http;

(new AllowCors)->init();

Http::setContentType('application/json');
header('Content-Type: application/json');

