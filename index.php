<?php
//Developed by Dairo Arce
namespace PH7\ApiSimpleMenu;

use Whoops\Run as WhoopsRun;
use Whoops\Handler\JsonResponseHandler as WhoopsJsonResponseHandler;

require __DIR__ . '/vendor/autoload.php';

$whoops = new WhoopsRun();
$whoops->pushHandler(new WhoopsJsonResponseHandler);
$whoops->register();

require __DIR__ . '/src/helpers/headers.inc.php';
require __DIR__ .'/src/config/config.inc.php';
require __DIR__ .'/src/config/database.inc.php';
require __DIR__ . '/src/routes/routes.php';
