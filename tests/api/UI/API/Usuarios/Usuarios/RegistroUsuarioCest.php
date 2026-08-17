<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Usuarios\Usuarios;

use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use App\Tests\Support\FunctionalTester;
use App\Usuarios\Domain\Usuario;
use Codeception\Template\Api;
use Symfony\Component\HttpFoundation\Response;

class RegistroUsuarioCest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
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
            'roles' => ['ROLE_USER'],
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
            'roles' => ['ROLE_USER'],
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
            'roles' => ['ROLE_USER'],
        ], JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'El email proporcionado no es válido'
        ]);
    }

}
