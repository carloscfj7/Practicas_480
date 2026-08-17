<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Cliente;

use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;

final class ClienteCreateCest
{
    public function _before(ApiTester $I): void
    {
        // Load the required fixtures
        $I->loadFixtures([UserFixture::class]);
    }

    public function testShouldCreateClienteAdminSuccessfully(ApiTester $I): void
    {
        $I->wantTo('create a new cliente successfully');

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $clienteData = [
            'email' =>  'cliente@ejemplo.com',
            'password' => 'securePassword123',
            'nombre' => 'Juan',
            'contacto' => 'Contacto Test',
            'direccion' => 'Calle Ficticia 123'
        ];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/cliente/create', json_encode($clienteData, JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['message' => 'Cliente creado correctamente']);
    }

    public function testShouldNotCreateClienteAdminWhenNoData(ApiTester $I): void
    {
        $I->wantTo('not create cliente when no data is provided');

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/cliente/create', json_encode([]));

        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson(['message' => 'Es obligatorio proporcionar un Json con los datos necesarios.']);
    }

    public function testShouldNotCreateClienteAdminWhenEmailAlreadyExists(ApiTester $I): void
    {
        $I->wantTo('not create cliente when email already exists');

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $clienteData = [
            'nombre' => 'Juan',
            'direccion' => 'Calle Ficticia 123',
            'contacto' => 'Contacto Test',
            'email' => 'carlos@prueba.com',
            'password' => 'securePassword123',
        ];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/cliente/create', json_encode($clienteData));

        $I->seeResponseCodeIs(409);
        $I->seeResponseContainsJson(['message' => 'Ya existe un usuario con ese email']);
    }
}
