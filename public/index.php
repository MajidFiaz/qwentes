<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Handlers\Strategies\RequestResponseArgs;
use DI\Bridge\Slim\Bridge as SlimBridge;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);

$dotenv->load();

require APP_ROOT . '/config/database.php';




$builder = new Container;


$app= SlimBridge::create($builder);

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true, true, true);

require APP_ROOT . '/routes/api.php';

$app->run();