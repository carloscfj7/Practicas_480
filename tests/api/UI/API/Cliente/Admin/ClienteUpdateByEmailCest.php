<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Cliente\Admin;

use App\Tests\api\DataFixtures\ClienteFixture;
use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use Symfony\Component\HttpFoundation\Response;

final class ClienteUpdateByEmailCest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
            ClienteFixture::class,
        ]);
    }

    public function testShouldUpdateClienteByEmailSuccessfully(ApiTester $I): void
    {
        $I->wantTo('update a cliente by email successfully as admin');
        $admin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $adminEmail = $admin->getEmail()->value();

        $I->sendPost('/login', json_encode([
            'email' => $adminEmail,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');

        $cliente = $I->grabFixtureReference(ClienteFixture::CLIENTE_REF);
        $clienteEmail = $cliente->getIdUsuario()->getEmail()->value();

        $payload = [
            'email' => $clienteEmail,
            'direccion' => 'Nueva dirección 123',
            'contacto' => '123456789',
        ];

        $I->sendPut('/admin/cliente/update', json_encode($payload, JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'message' => 'Datos actualizados correctamente',
            'direccion' =>'Nueva dirección 123',
            'contacto' => '123456789'
        ]);
    }

    public function testShouldFailIfNoDataProvided(ApiTester $I): void
    {
        $I->wantTo('get 400 if no JSON is provided');

        $admin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $adminEmail = $admin->getEmail()->value();

        $I->sendPost('/login', json_encode([
            'email' => $adminEmail,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPut('/admin/cliente/update', '');

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson(['message' => 'Es obligatorio proporcionar un Json con los datos necesarios.']);
    }

    public function testShouldFailIfClienteDoesNotExist(ApiTester $I): void
    {
        $I->wantTo('get 404 if cliente does not exist');

        $admin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $adminEmail = $admin->getEmail()->value();

        $I->sendPost('/login', json_encode([
            'email' => $adminEmail,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');

        $payload = [
            'email' => 'noexiste@cliente.com',
            'direccion' => 'Algo',
            'contacto' => '00000000',
        ];

        $I->sendPut('/admin/cliente/update', json_encode($payload, JSON_THROW_ON_ERROR));

        $I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
        $I->seeResponseContainsJson(['message' => 'El cliente no existe']);
    }
}
