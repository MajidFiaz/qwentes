<?php

namespace App\Controllers;

use App\AppController;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Utilities\JsonRenderer;
use Respect\Validation\Validator as v;
use Illuminate\Support\Facades\Hash;

class UsersController extends AppController
{



    public function create(Request $request, Response $response): Response
    {

        $validation=$this->validator->validate($request, [
            'givenName' => v::notEmpty()->stringType(),
            'familyName' => v::notEmpty()->stringType(),
            'email' => v::notEmpty()->email(),
            'dateOfBirth' => v::notEmpty()->date(),
            'password' => v::noWhitespace()->stringType()->notEmpty()->regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[,.:;-_$%&()=]{2})(?=.{6,}$)/')->regex('/^(?:(.)(?!\1))*$/'),
            'address' => v::key('street',v::notEmpty()->stringType()->regex('/(?=.{3,}$)/'))
                ->key('city',v::notEmpty()->stringType()->regex('/(?=.{3,}$)/'))
                ->key('postalCode',v::notEmpty()->stringType()->regex('/(?=.{3,}$)/'))
                ->key('countryCode',v::notEmpty()->stringType()->regex('/^[A-Z]{2}$/'))
                ->key('latitude',v::key('lat',v::notEmpty()->floatType())->key('lng',v::notEmpty()->floatType())),
        ]);
        if ($validation->failed()) {
            return $this->renderer->json($response, [
                "error" =>  $validation->errors
            ])->withStatus(422);
        }
        $email=$request->getParsedBody()['email'];
        $prefix = substr($email, 0, strrpos($email, '@'));
        if (str_contains($request->getParsedBody()['password'], $prefix)) {
            return $this->renderer->json($response, [
               "password" => "Password cannot contain first part of email"
            ])->withStatus(422);
        }
        $email_check=User::where("email",$email)->first();
        if ($email_check) {
            return $this->renderer->json($response, ["error" => ["email" => "Email already exists"]])->withStatus(409);
        }

        $user=new User();
        $user->givenName = $request->getParsedBody()['givenName'];
        $user->familyName = $request->getParsedBody()['familyName'];
        $user->email = $email;
        $user->dateOfBirth = $request->getParsedBody()['dateOfBirth'];
        $user->password = password_hash($request->getParsedBody()['password'], PASSWORD_BCRYPT);
        $user->street = $request->getParsedBody()['address']['street'];
        $user->city = $request->getParsedBody()['address']['city'];
        $user->postalCode = $request->getParsedBody()['address']['postalCode'];
        $user->countryCode = $request->getParsedBody()['address']['countryCode'];
        $user->lat = $request->getParsedBody()['address']['latitude']['lat'];
        $user->lng = $request->getParsedBody()['address']['latitude']['lng'];
        $user->save();
        return $this->renderer->json($response,$user);
    }

    public function index(Request $request, Response $response): Response
    {
        $users=User::all();
        $output=[
            "totalItems"=>count($users),
            "items"=>$users
        ];
        return $this->renderer->json($response, $output);
    }

    public function getByEmail(Request $request, Response $response,$email): Response
    {
        $user=User::where("email",$email)->first();

        if ($user) {
            return $this->renderer->json($response, $user);
        }else{
            return $this->renderer->json($response, ["error"=>"Email not found"])->withStatus(404);
        }
    }

    public function updateByEmail(Request $request, Response $response,$email): Response
    {
        $user=User::where("email",$email)->first();

        if ($user) {
            $validation=$this->validator->validate($request, [
                'givenName' => v::notEmpty()->stringType(),
                'familyName' => v::notEmpty()->stringType(),
                'email' => v::notEmpty()->email(),
                'dateOfBirth' => v::date(),
                'password' => v::noWhitespace()->stringType()->notEmpty()->regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[,.:;-_$%&()=]{2})(?=.{6,}$)/')->regex('/^(?:(.)(?!\1))*$/'),
                'address' => v::key('street',v::notEmpty()->stringType()->regex('/(?=.{3,}$)/'))
                    ->key('city',v::notEmpty()->stringType()->regex('/(?=.{3,}$)/'))
                    ->key('postalCode',v::notEmpty()->stringType()->regex('/(?=.{3,}$)/'))
                    ->key('countryCode',v::notEmpty()->stringType()->regex('/^[A-Z]{2}$/'))
                    ->key('latitude',v::key('lat',v::notEmpty()->floatType())->key('lng',v::notEmpty()->floatType())),
            ]);
            if ($validation->failed()) {
                return $this->renderer->json($response, [
                    "error" =>  $validation->errors
                ])->withStatus(422);
            }
            if ($request->getParsedBody()['email']!=$user->email) {
                $email_check = User::where("email", $request->getParsedBody()['email'])->first();
                if ($email_check) {
                    return $this->renderer->json($response, ["error" => ["email" => "Email already exists"]]);
                }
                $user->email = $request->getParsedBody()['email'];
            }
            $user->givenName = $request->getParsedBody()['givenName'];
            $user->familyName = $request->getParsedBody()['familyName'];

            if (isset($request->getParsedBody()['dateOfBirth'])) {
                $user->dateOfBirth = $request->getParsedBody()['dateOfBirth'];
            }
            $user->street = $request->getParsedBody()['address']['street'];
            $user->city = $request->getParsedBody()['address']['city'];
            $user->postalCode = $request->getParsedBody()['address']['postalCode'];
            $user->countryCode = $request->getParsedBody()['address']['countryCode'];
            $user->lat = $request->getParsedBody()['address']['latitude']['lat'];
            $user->lng = $request->getParsedBody()['address']['latitude']['lng'];
            $user->update();

            return $this->renderer->json($response, $user);
        }else{
            return $this->renderer->json($response, ["error"=>"Email not found"])->withStatus(404);
        }
    }


}