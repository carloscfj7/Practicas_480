<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Services\Proyectos;

use App\Clientes\Application\Exceptions\ClienteNotFoundException;
use App\Clientes\Domain\Cliente;
use App\Clientes\Domain\ClienteRepositoryInterface;
use App\Consultores\Application\Exceptions\Consultor\ConsultorNotFoundException;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Services\Proyectos\ProyectoCreateService;
use App\Proyectos\Domain\Exceptions\Proyecto\ExistentProyectoException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Shared\Application\Exceptions\InvalidDateException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoCreateServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ClienteRepositoryInterface $clienteRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->clienteRepository = $this->createMock(ClienteRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
    }

    private function createMockCliente(): Cliente
    {
        return $this->createMock(Cliente::class);
    }
    public function testShouldCreateProyectoSuccessfully(): void
    {
        $data = [
            'nombre' => 'Proyecto Nuevo',
            'descripcion' => 'Descripción del proyecto',
            'fecha_ini' => '2025-04-16',
            'fecha_fin' => '2025-06-30',
            'estado' => 'en proceso',
            'email_cliente' => 'cliente@test.com',
            'consultores' => ['consultor1@test.com']
        ];

        $cliente = $this->createMockCliente();

        $this->createMock(Proyecto::class);

        $this->proyectoRepository
            ->method('validateExistentProyecto')
            ->with($data['nombre']);

        $this->clienteRepository
            ->expects($this->once())
            ->method('validateClienteOrFails')
            ->with($data['email_cliente'])
            ->willReturn($cliente);

        $this->consultorRepository
            ->expects($this->once())
            ->method('addConsultoresToProyecto')
            ->with($this->isInstanceOf(Proyecto::class), $data['consultores']);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Proyecto::class));

        $service = new ProyectoCreateService($this->proyectoRepository, $this->clienteRepository, $this->consultorRepository);

        $response = $service($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('El proyecto se ha creado correctamente', $content['message']);
        $this->assertEquals('Proyecto Nuevo', $content['nombre']);
    }


    public function testShouldThrowInvalidDateException(): void
    {
        $this->expectException(InvalidDateException::class);

        $data = [
            'nombre' => 'Proyecto con fecha inválida',
            'descripcion' => 'desc',
            'fecha_ini' => 'fecha inválida',
            'estado' => 'en proceso',
            'email_cliente' => 'cliente@test.com',
            'consultores' => ['consultor1@test.com']
        ];

        $this->proyectoRepository
            ->method('validateExistentProyecto');

        $service = new ProyectoCreateService($this->proyectoRepository, $this->clienteRepository, $this->consultorRepository);
        $service($data);
    }

    public function testShouldThrowInvalidDateRangeException(): void
    {
        $this->expectException(InvalidDateRangeExcpecion::class);

        $data = [
            'nombre' => 'Proyecto con fechas incorrectas',
            'descripcion' => 'desc',
            'fecha_ini' => '2025-06-30',
            'fecha_fin' => '2025-04-16',
            'estado' => 'en proceso',
            'email_cliente' => 'cliente@test.com',
            'consultores' => ['consultor1@test.com']
        ];

        $this->proyectoRepository
            ->method('validateExistentProyecto');

        $service = new ProyectoCreateService($this->proyectoRepository, $this->clienteRepository, $this->consultorRepository);
        $service($data);
    }

}
