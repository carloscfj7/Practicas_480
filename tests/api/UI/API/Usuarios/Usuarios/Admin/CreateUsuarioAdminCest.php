<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Usuarios\Usuarios\Admin;

use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use App\Usuarios\Domain\Usuario;
use Symfony\Component\HttpFoundation\Response;

class CreateUsuarioAdminCest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
        ]);
    }
    public function testUsuarioPuedeRegistrarseCorrectamente(ApiTester $I): void
    {

        $I->wantTo('test usuario puede registrarse correctamente');
        $email = 'usuario@example.com';


        $I -> haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/register', json_encode([
            'email' => $email,
            'password' => 'Password',
            'roles' => ['ROLE_ADMIN'],
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseIsJson();
        $I->seeInRepository(Usuario::class, [
            'email' => $email,
        ]);
    }

    public function testNoSePuedeRegistrarUsuarioConEmailExistente(ApiTester $I): void
    {
        $I->wantTo('test no se puede registrar usuario con email existente');

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);

        $email = $usuario->getEmail()->value();

        $I->sendPOST('/register', json_encode([
            'email' => $email,
            'password' => 'Password',
            'roles' => ['ROLE_ADMIN'],
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_CONFLICT);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Ya existe un usuario con ese email'
        ]);
    }

    public function testRegistroFallaSiFaltanDatos(ApiTester $I): void
    {
        $I->wantTo('test registro falla si faltan datos');
        $I->sendPOST('/register', json_encode([
            'email' => 'existente@example.com',
        ], JSON_THROW_ON_ERROR));
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
    }


    public function testNoSePuedeRegistrarUsuarioConContrasenaIncorrecta(ApiTester $I): void
    {
        $I->wantTo('test no se puede registrar usuario con email existente');


        $email = 'incorrecta@example.com';

        $I->sendPOST('/register', json_encode([
            'email' => $email,
            'password' => 'Pas',
            'roles' => ['ROLE_ADMIN'],
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'La contraseña debe tener al menos 6 caracteres'
        ]);
    }

    public function testNoSePuedeRegistrarUsuarioConEmailIncorrecta(ApiTester $I): void
    {
        $I->wantTo('test no se puede registrar usuario con email existente');


        $email = 'incorrectexample.com';

        $I->sendPOST('/register', json_encode([
            'email' => $email,
            'password' => 'Password',
            'roles' => ['ROLE_ADMIN'],
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'El email proporcionado no es válido'
        ]);
    }

}
