<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application;

use App\Clientes\Application\Services\ClienteDeleteService;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ClienteDeleteServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;


    protected function setUp(): void
    {
        parent::setUp();
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
    }

    public function testShouldDeleteClienteSuccesfully()
    {
        $email = new Email('cliente@ejemplo.com');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);
        $cliente = $this->createMock(Cliente::class);


        $this->clienteRepository
            ->expects($this->once())
            ->method('findByEmailUsuario')
            ->with($email->value())
            ->willReturn($cliente);

        $this->clienteRepository
            ->expects($this->once())
            ->method('remove')
            ->with($cliente);

        $service = new ClienteDeleteService(
            $this->clienteRepository
        );

        $response = $service($usuario);

        $this->assertEquals('Cliente eliminado correctamente', $response);
    }

}