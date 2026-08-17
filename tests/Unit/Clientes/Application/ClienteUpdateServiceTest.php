<?php

declare(strict_types=1);

namespace App\Tests\Unit\Clientes\Application;

use App\Clientes\Application\Dto\DataRequest\ClienteUpdateRequestDto;
use App\Clientes\Application\Dto\DataResponse\ClienteUpdateResponseDto;
use App\Clientes\Application\Services\ClienteUpdateService;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Shared\Application\Dto\Response\UpdateServicesResponseDto;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;

class ClienteUpdateServiceTest extends Unit
{
    private ClienteRepositoryInterface $clienteRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
    }

    private function createUsuarioConEmail(string $email)
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email($email));
        return $usuario;
    }

    private function createClienteMock(string $contacto = 'Contacto 1', string $direccion = 'Direccion 1')
    {
        $cliente = $this->createMock(Cliente::class);
        $cliente->method('getContacto')->willReturn($contacto);
        $cliente->method('getDireccion')->willReturn($direccion);
        return $cliente;
    }

    private function assertOkResponse(UpdateServicesResponseDto $response, array $expectedData): void
    {
        $this->assertEquals(
            'Datos actualizados correctamente',
            $response->message
        );
        $this->assertEquals($expectedData, $response->actualizacion);
    }

    public function testShouldUpdateAllClienteSuccessfully(): void
    {
        $usuario = $this->createUsuarioConEmail('cliente@example.com');
        $cliente = $this->createClienteMock();

        $cliente->expects($this->once())->method('setContacto')->with('Contacto 2');
        $cliente->expects($this->once())->method('setDireccion')->with('Direccion 2');

        $this->clienteRepository->method('validateClienteOrFails')->willReturn($cliente);
        $this->clienteRepository->expects($this->once())->method('save')->with($cliente);

        $service = new ClienteUpdateService($this->clienteRepository);
        $data = new ClienteUpdateRequestDto('Contacto 2', 'Direccion 2');
        $expectedResponse = ['contacto' => 'Contacto 2', 'direccion' => 'Direccion 2'];
        $response = $service($usuario, $data);

        $this->assertOkResponse($response, $expectedResponse);
    }

    public function testShouldUpdateContactoClienteSuccessfully(): void
    {
        $usuario = $this->createUsuarioConEmail('cliente@example.com');
        $cliente = $this->createClienteMock();

        $cliente->expects($this->once())->method('setContacto')->with('Contacto 2');

        $this->clienteRepository->method('validateClienteOrFails')->willReturn($cliente);
        $this->clienteRepository->expects($this->once())->method('save')->with($cliente);

        $service = new ClienteUpdateService($this->clienteRepository);
        $data = new ClienteUpdateRequestDto('Contacto 2');

        $expectedResponse = ['contacto' => 'Contacto 2'];
        $response = $service($usuario, $data);

        $this->assertOkResponse($response, $expectedResponse);

    }

    public function testShouldUpdateDireccionClienteSuccessfully(): void
    {
        $usuario = $this->createUsuarioConEmail('cliente@example.com');
        $cliente = $this->createClienteMock();

        $cliente->expects($this->once())->method('setDireccion')->with('Direccion 2');

        $this->clienteRepository->method('validateClienteOrFails')->willReturn($cliente);
        $this->clienteRepository->expects($this->once())->method('save')->with($cliente);

        $service = new ClienteUpdateService($this->clienteRepository);
        $data = new ClienteUpdateRequestDto(direccion: 'Direccion 2');

        $expectedResponse = ['direccion' => 'Direccion 2'];
        $response = $service($usuario, $data);

        $this->assertOkResponse($response, $expectedResponse);

    }

    public function testShouldNotUpdateClienteSuccessfully(): void
    {
        $usuario = $this->createUsuarioConEmail('cliente@example.com');
        $cliente = $this->createClienteMock();

        $this->clienteRepository->method('validateClienteOrFails')->willReturn($cliente);

        $service = new ClienteUpdateService($this->clienteRepository);

        $response = $service($usuario, new ClienteUpdateRequestDto());
        $this->assertEquals(
            'No se ha insertado ningún valor que se pueda modificar o los valores insertados son iguales que los actuales',
            $response->message
        );
    }
}
