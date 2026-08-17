<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Cliente\Admin;

use App\Tests\api\DataFixtures\ClienteFixture;
use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use Symfony\Component\HttpFoundation\Response;

final class ClienteReadByEmailAdminCest
{
    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
            ClienteFixture::class,
        ]);
    }

    public function testShouldReadClienteByEmailAdminSuccessfully(ApiTester $I): void
    {
        $I->wantTo('read a cliente by email successfully as admin');
        $usuarioAdmin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $emailAdmin = $usuarioAdmin->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $emailAdmin,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_2);
        $emailCliente = $usuario->getEmail()->value();

        $I->sendGet('/admin/cliente/read?email='. urlencode($emailCliente) );


        $cliente = $I->grabFixtureReference(ClienteFixture::CLIENTE_REF);
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'Los datos del cliente son: ']);
        $I->seeResponseContainsJson(['cliente' => [
            'id' => $cliente->getId()->__toString(),
            'nombre' => $cliente->getNombre(),
            'direccion' => $cliente->getDireccion(),
            'contacto' => $cliente->getContacto(),
        ]]
        );
    }

    public function testShouldReturnErrorIfEmailNotProvided(ApiTester $I): void
    {
        $I->wantTo('return an error if email is not provided');
        $usuarioAdmin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $emailAdmin = $usuarioAdmin->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $emailAdmin,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGet('/admin/cliente/read');


        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => "Faltan datos obligatorios"]);
    }

    public function testShouldReturnNotFoundIfClienteDoesNotExist(ApiTester $I): void
    {
        $I->wantTo('return not found if cliente does not exist');
        $usuarioAdmin = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $emailAdmin = $usuarioAdmin->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $emailAdmin,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendGet('/admin/cliente/read?email= '. urlencode('notExisting@error.com'));

        $I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => 'El cliente no existe']);
    }
}