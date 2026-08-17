<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Admin;

use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadByClienteService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadByClienteServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ClienteRepositoryInterface $clienteRepository;
    private ProyectoDto $proyectoDto;

    protected function setUp(): void
    {
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->proyectoDto = $this->createMock(ProyectoDto::class);
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
        $email = 'cliente@test.com';
        $cliente = $this->mockCliente();
        $proyectos = [$this->mockProyecto()];
        $dtoArray = [['nombre' => 'Proyecto Test']];

        $this->clienteRepository
            ->expects($this->once())
            ->method('validateClienteOrFails')
            ->with($email)
            ->willReturn($cliente);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('getProyectosByCliente')
            ->with($cliente)
            ->willReturn($proyectos);

        $this->proyectoDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($proyectos)
            ->willReturn($dtoArray);

        $service = new ProyectoReadByClienteService(
            $this->proyectoRepository,
            $this->clienteRepository,
            $this->proyectoDto
        );

        $response = $service(['email' => $email]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals("Estos todos los proyectos del consultor con email: $email", $content['message']);
        $this->assertEquals($dtoArray, $content['proyectos']);
    }

    public function testShouldNotReadAnyProyectoByCliente(): void
    {
        $email = 'cliente@test.com';
        $cliente = $this->mockCliente();

        $this->clienteRepository
            ->expects($this->once())
            ->method('validateClienteOrFails')
            ->with($email)
            ->willReturn($cliente);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('getProyectosByCliente')
            ->with($cliente)
            ->willReturn([]);

        $this->proyectoDto
            ->expects($this->never())
            ->method('collectionFromEntities');

        $service = new ProyectoReadByClienteService(
            $this->proyectoRepository,
            $this->clienteRepository,
            $this->proyectoDto
        );

        $response = $service(['email' => $email]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals("El cliente $email no tiene ningun proyecto asociado", $content['message']);
        $this->assertArrayNotHasKey('proyectos', $content);
    }

}
