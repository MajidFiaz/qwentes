<?php

use App\Console\UserCreateCommand;
use Symfony\Component\Console\Application;


define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);

$dotenv->load();

require APP_ROOT . '/config/database.php';


try {


    $application = new Application();

    $application->add(new UserCreateCommand());

    $application->run();
} catch (Throwable $exception) {
    echo $exception->getMessage();
    exit(1);
}
