<?php

namespace App\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Utilities\JsonRenderer;

class ApiAuthentication
{

    private JsonRenderer $renderer;
    private Response $res;

    public function __construct(JsonRenderer $renderer, Response $res)
    {
        $this->renderer = $renderer;
        $this->res = $res;
    }
    public function __invoke($request, RequestHandler $handler)
    {
        try {
            if ($request->hasHeader('content-type') && $request->getHeader('content-type')[0] == 'application/json' && $request->hasHeader('accept') && $request->getHeader('accept')[0] == 'application/json') {

                if ($request->hasHeader("Authorization")) {
                    $header = $request->getHeader("Authorization");

                    if (!empty($header)) {
                        $bearer = trim($header[0]);
                        preg_match("/Bearer\s(\S+)/", $bearer, $matches);
                        $token = $matches[1];

                        $key = new Key($_ENV['SECRET_KEY'], "HS256");
                        $dataToken = JWT::decode($token, $key);
                        $now = (new \DateTime("now"))->format("Y-m-d H:i:s");

                        if (@$dataToken->exp < $now) {
                            return $this->renderer->json($this->res, [
                                "Error" => [
                                    "Message" => "Token expired!"
                                ]
                            ])->withStatus(401);
                        }
                    }
                } else {
                    return $this->renderer->json($this->res, [
                        "Error" => [
                            "Message" => "Authentication Errors"
                        ]
                    ])->withHeader("Content-Type", "application/json")
                        ->withStatus(401);
                }
            } else {
                return $this->renderer->json($this->res, [
                    "Error" => [
                        "Message" => "Content type and Accept only accepts application/json"
                    ]
                ])->withHeader("Content-Type", "application/json")
                    ->withStatus(415);
            }
        } catch (\Exception $e) {
            return $this->renderer->json($this->res, [
                "Error" => [
                    "Message" => $e->getMessage()
                ]
            ])->withHeader("Content-Type", "application/json")
                ->withStatus(500);
        }
        return $handler->handle($request);
    }
}