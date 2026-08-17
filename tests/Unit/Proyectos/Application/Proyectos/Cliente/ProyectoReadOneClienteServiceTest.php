<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Cliente;

use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Cliente\ProyectoReadOneClienteService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadOneClienteServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ClienteRepositoryInterface $clienteRepository;
    private ProyectoDto $proyectoDto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->proyectoDto = $this->createMock(ProyectoDto::class);
    }

    private function mockUsuario(string $email): Usuario
    {
        $usuario = $this->createMock(Usuario::class);
        $emailValue = $this->createMock(Email::class);
        $emailValue->method('value')->willReturn($email);
        $usuario->method('getEmail')->willReturn($emailValue);

        return $usuario;
    }

    private function mockCliente(): Cliente
    {
        return $this->createMock(Cliente::class);
    }

    private function mockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldReadProyectoByClienteSuccessfully(): void
    {
        $usuario = $this->mockUsuario('cliente@example.com');
        $cliente = $this->mockCliente();
        $proyecto = $this->mockProyecto();

        $this->clienteRepository
            ->expects($this->once())
            ->method('validateClienteOrFails')
            ->with('cliente@example.com')
            ->willReturn($cliente);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombreAndCliente')
            ->with('Proyecto Test', $cliente)
            ->willReturn($proyecto);

        $this->proyectoDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($proyecto)
            ->willReturn($this->proyectoDto);

        $service = new ProyectoReadOneClienteService($this->proyectoRepository, $this->clienteRepository, $this->proyectoDto);
        $response = $service(['nombre' => 'Proyecto Test'], $usuario);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Estos son los datos del proyecto', $content['message']);
    }


}
