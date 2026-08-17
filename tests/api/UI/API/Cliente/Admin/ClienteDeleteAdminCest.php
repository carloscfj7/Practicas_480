<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Cliente\Admin;

use App\Tests\api\DataFixtures\ClienteFixture;
use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;


final class ClienteDeleteAdminCest
{

    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class,
            ClienteFixture::class
        ]);
    }
    public function testShouldDeleteClienteAdminSuccessfully(ApiTester $I): void{
        $I->wantTo('delete cliente by they email successfully');
        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $email = $I->grabFixtureReference(UserFixture::USUARIO_2)->getEmail()->value();
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendDelete('/admin/cliente/delete?email='. urlencode($email));

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['message' => 'Cliente eliminado correctamente']);
    }

    public function testShouldNotDeleteClienteAdminWhenNoData(ApiTester $I): void{
        $I->wantTo('Dont delete cliente when no data');
        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_1);
        $email = $usuario->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDelete('/admin/cliente/delete');
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson(['message' => 'Faltan datos obligatorios']);
    }

}