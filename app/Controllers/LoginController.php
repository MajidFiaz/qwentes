<?php

namespace App\Controllers;

use App\AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\User;
use Firebase\JWT\JWT;
use App\Utilities\JsonRenderer;
use Respect\Validation\Validator as v;

final class LoginController extends AppController
{
    private User $user;

    public function __construct( User $user)
    {
        $this->user = $user;
        parent::__construct();
    }

    public function index(Request $request, Response $response): Response
    {

        $validation=$this->validator->validate($request, [
            'email' => v::notEmpty()->email(),
            'password' => v::noWhitespace()->stringType()->notEmpty(),
        ]);
        if ($validation->failed()) {
            return $this->renderer->json($response, [
                "error" =>  $validation->errors,
            ])->withStatus(422);
        }
        try {
            $result = $this->user->getUserByEmail($request->getParsedBody()['email']);
            if ($result) {
                if (password_verify($request->getParsedBody()['password'], $result['password'])) {
                    $expire = (new \DateTime("now"))->modify("+$_ENV[EXPIRE_TIME] minutes")->format("Y-m-d H:i:s");
                    $payload=[
                        'name'=>$result['givenName']." ".$result['familyName'],
                        'email'=>$result['email'],
                        'given_name' => $result['givenName'],
                        'family_name' => $result['familyName'],
                        'exp' => $expire,
                        'iat' => time(),
                    ];

                    $token = JWT::encode($payload, $_ENV['SECRET_KEY']);
                    return $this->renderer->json($response, [
                        "jwt" => $token
                    ])->withStatus(200);
                } else {
                    return $this->renderer->json($response, [
                        "error" => ["password" => "password is incorrect"],
                    ])->withStatus(422);
                }
            } else {
                return $this->renderer->json($response, [
                    "Error" => [
                        "Message" => "Email is incorrect",
                    ]
                ])->withStatus(422);
            }
        } catch (\PDOException $e) {
            return $this->renderer->json($response, [
                "Error" => [
                    "Message" => $e->getMessage()
                ]
            ])->withHeader("Content-Type", "application/json")
                ->withStatus(500);
        }
    }
}