<?php

namespace App\Utilities;

use Psr\Http\Message\ResponseInterface;

final class JsonRenderer
{
    public function json(ResponseInterface $response, $data = null, int $options = 0): ResponseInterface
    {
        $response = $response->withHeader("Content-Type", "application/json");
        $response->getBody()->write((string)json_encode($data, $options));

        return $response;
    }
}