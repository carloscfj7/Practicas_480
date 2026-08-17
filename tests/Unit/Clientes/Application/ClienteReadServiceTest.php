<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application;

use App\Clientes\Application\Dto\Entity\ClienteDto;
use App\Clientes\Application\Services\ClienteReadService;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Clientes\Domain\ValueObjects\ClienteId;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\Email;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ClienteReadServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;
    private ClienteDto $clienteDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->clienteDto = $this->createMock(ClienteDto::class);
    }

    public function testShouldReadClienteSuccesfully()
    {
        $email = new Email('Cliente@ejemplo.com');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);

        $cliente = $this->createMock(Cliente::class);
        $ulid = new ClienteId();

        $this->clienteRepository
            ->expects($this->once())
            ->method('findByEmailUsuario')
            ->with($email->value())
            ->willReturn($cliente);
        $this->clienteDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($cliente)
            ->willReturn($this->clienteDto);
        $this->clienteDto->id = $ulid;

        $service = new ClienteReadService($this->clienteRepository, $this->clienteDto);
        $response = $service($usuario);

        $this->assertEquals($ulid,$response->id);
    }

}