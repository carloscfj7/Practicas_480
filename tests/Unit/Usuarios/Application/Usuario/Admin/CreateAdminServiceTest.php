<?php

declare(strict_types=1);

namespace App\Tests\Unit\Usuarios\Application\Usuario\Admin;

use App\Usuarios\Application\Dto\Usuario\DataRequest\CredentialsDto;
use App\Usuarios\Application\Services\Usuario\Admin\CreateAdminService;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use Codeception\Test\Unit;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CreateAdminServiceTest extends Unit
{
    private RegistroUsuarioService $registroService;
    private CreateAdminService $service;

    protected function setUp(): void
    {
        $this->registroService = $this->createMock(RegistroUsuarioService::class);
        $this->service = new CreateAdminService($this->registroService);
    }

    public function testShouldCreateAdminWhenValidDataIsProvided(): void
    {
        $data =  new CredentialsDto(email: 'admin@example.com', password: 'securepassword');

        $response = $this->service->__invoke($data);
        $this->assertEquals('Admin creado correctamente', $response->message);
        $this->assertEquals('admin@example.com', $response->email);
    }

}
