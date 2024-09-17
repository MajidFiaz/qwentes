<?php

namespace App;



use App\Utilities\JsonRenderer;
use App\Validation\Validator;
use DI\Container;
use Psr\Container\ContainerInterface;
class AppController
{
    public JsonRenderer $renderer;
    public Validator $validator;

    public function __construct()
    {
        $this->renderer = new JsonRenderer();
        $this->validator= new Validator();

    }

}