<?php

declare(strict_types=1);

namespace App\Tests\api\Endpoints\API\Usuarios\Usuarios;

use App\Tests\api\DataFixtures\UserFixture;
use App\Tests\Support\ApiTester;
use Codeception\Util\HttpCode;

class DeleteUsuarioCest
{

    public function _before(ApiTester $I): void
    {
        $I->loadFixtures([
            UserFixture::class
        ]);
    }

    public function testShuoldDeleteUsuarioSucessfully(ApiTester $I):void
    {
        $I->wantTo("test should delete usuario sucessfully");

        $usuario = $I->grabFixtureReference(UserFixture::USUARIO_2);
        $email = $usuario->getEmail()->value();
        $I->sendPost('/login', json_encode([
            'email' => $email,
            'password' => 'contraseña',
        ], JSON_THROW_ON_ERROR));

        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendDELETE('/delete');

        $I->seeResponseCodeIs(HttpCode::OK);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' =>  'Usuario eliminado correctamente'
        ]);
    }
}