<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Cliente\Admin;

use App\Tests\api\DataFixtures\ClienteFixture;
use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use Symfony\Component\HttpFoundation\Response;

final class ClienteReadAllAdminCest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
            ClienteFixture::class,
        ]);
    }

    public function testShouldReadAllClientesAdminSuccessfullyWithData(ApiTester $I): void
    {
        $I->wantTo('read all clientes by admin successfully when there is data');
        $usuarioAdmin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $emailAdmin = $usuarioAdmin->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $emailAdmin,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('/admin/cliente/read/all');

        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Los datos de los clientes son: ']);
        $cliente = $I->grabFixtureReference(ClienteFixture::CLIENTE_REF);
        $I->seeResponseContainsJson(['clientes' => [
            [
                'id' => $cliente->getId()->__toString(),
                'nombre' => $cliente->getNombre(),
                'direccion' => $cliente->getDireccion(),
                'contacto' => $cliente->getContacto(),

            ],
        ]]);
    }

    public function testShouldReadAllClientesAdminSuccessfullyWithNoData(ApiTester $I): void
    {
        $I->wantTo('read all clientes by admin successfully when there is no data');
        // Load only the user fixture to ensure no clients are present
        $I->loadFixtures([UserFixture::class]);

        $usuarioAdmin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $emailAdmin = $usuarioAdmin->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $emailAdmin,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGet('/admin/cliente/read/all');

        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'No existen clientes']);
        $I->dontSeeResponseContainsJson(['clientes']);
    }
}