<?php
declare(strict_types=1);
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UsersController;
use App\Controllers\LoginController;
use App\Middleware\ApiAuthentication;

$app->group('', function (RouteCollectorProxy $group) {
    $group->post('/login', [LoginController::class,'index']);
});


$app->group('', function (RouteCollectorProxy $group) {
    $group->post('/users', [UsersController::class,"create"]);
    $group->get('/users', [UsersController::class,"index"]);
    $group->get('/users/{email}', [UsersController::class,"getByEmail"]);
    $group->put('/users/{email}', [UsersController::class,"updateByEmail"]);
})->add(ApiAuthentication::class);




