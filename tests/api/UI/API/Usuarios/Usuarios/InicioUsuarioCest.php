<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Usuarios\Usuarios;

use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use Symfony\Component\HttpFoundation\Response;

class InicioUsuarioCest
{

    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
        ]);
    }
    public function testUsuarioPuedelogueareseCorrectamente(ApiTester $I): void
    {

        $I->wantTo('test usuario puede loguearse correctamente');

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();



        $I -> haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Este es el token de inicio de sesion'
        ]);
    }

    public function testNoSePuedeLoguearSinEstarRegistrado(ApiTester $I): void
    {
        $I->wantTo('test no se puede loguear si no esa registrado');

        $email = 'nuevo@example.com';


        $I->sendPOST('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' =>  'Las credenciales introducidas no son correctas.'

        ]);
    }

    public function testUsuarioNoPuedelogueareseContrasenaIncorrecta(ApiTester $I): void
    {

        $I->wantTo('test usuario puede loguearse correctamente');

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();



        $I -> haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/login', json_encode([
            'email' => $email,
            'password' => 'incorrecto',
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' =>  'Las credenciales introducidas no son correctas.'

        ]);
    }
    public function testRegistroFallaSiFaltanDatos(ApiTester $I): void
    {
        $I->wantTo('test registro falla si faltan datos');
        $I->sendPOST('/login', json_encode([
            'email' => 'existente@example.com',
        ], JSON_THROW_ON_ERROR));
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
    }
}