<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application;

use App\Clientes\Application\Dto\DataRequest\ClienteCreateRequestDto;
use App\Clientes\Application\Services\ClienteCreateService;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Usuarios\Application\Services\Usuario\RegistroUsuarioService;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClienteCreateServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;
    private UsuarioRepositoryInterface $usuarioRepository;

    private RegistroUsuarioService $registroService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->registroService = $this->createMock(RegistroUsuarioService::class);
    }

    public function testCreateClienteSuccessfully(): void
    {
        $data = new ClienteCreateRequestDto('cliente21@example.com',
            'password',
            'Nombre',
            'Contacto 1',
            'Direccion 1');
        $email = new Email($data->email);

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);


        $this->usuarioRepository
            ->expects($this->once())
            ->method('validateUsuario')
            ->with($data->email)
            ->willReturn($usuario);

        $this->clienteRepository
            ->expects($this->once())
            ->method('save');

        $service = new ClienteCreateService(
            $this->clienteRepository,
            $this->usuarioRepository,
            $this->registroService
        );

        $result = $service($data);

        $this->assertEquals('Cliente creado correctamente', $result->message);

    }


}