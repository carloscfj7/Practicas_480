<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadUpdateAdminService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadUpdateAdminServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp():void
    {
        parent::setUp();
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
    }

    private function createMockDisponibilidad()
    {
        $disponibilidad = $this->createMock(Disponibilidad::class);
        $disponibilidad->method('isDisponible')->willReturn(true);
        $fecha_fin = \DateTime::createFromFormat('Y-m-d H:i:s', "2025-05-12 15:30:00");
        $disponibilidad->method('getFechaFin')->willReturn($fecha_fin);
        return $disponibilidad;

    }

    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }

    public function testShouldUpdateAllDisponibilidadSuccessfully(): void
    {
        $data = [
            "consultor" => "consultor@ejemplo.com",
            "fecha_ini" => "2025-04-24 15:30:00",
            "disponible" => false,
            "fecha_fin" => "2025-05-14 15:30:00"
        ];

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);


        $disponibilidad
            ->expects($this->once())
            ->method('setDisponible');

        $disponibilidad
            ->expects($this->once())
            ->method('setFechaFin');

        $service = new DisponibilidadUpdateAdminService($this->disponibilidadRepository, $this->consultorRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizacion', $content);

        $this->assertEquals([
            "disponible" => false,
            "fecha_fin" => "2025-05-14 15:30:00"
        ], $content['actualizacion']);
    }

    public function testShouldUpdateFechaFinDisponibilidadSuccessfully(): void
    {
        $data = [
            "consultor" => "consultor@ejemplo.com",
            "fecha_ini" => "2025-04-24 15:30:00",
            "fecha_fin" => "2025-05-14 15:30:00"
        ];


        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);

        $disponibilidad
            ->expects($this->once())
            ->method('setFechaFin');


        $service = new DisponibilidadUpdateAdminService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizacion', $content);

        $this->assertEquals([
            "fecha_fin" => "2025-05-14 15:30:00"
        ], $content['actualizacion']);
    }

    public function testShouldUpdateDisponibleDisponibilidadSuccessfully(): void
    {
        $data = [
            "consultor" => "consultor@ejemplo.com",
            "fecha_ini" => "2025-04-24 15:30:00",
            "disponible" => false,
        ];


        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);


        $disponibilidad
            ->expects($this->once())
            ->method('setDisponible');


        $service = new DisponibilidadUpdateAdminService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizacion', $content);

        $this->assertEquals([
            "disponible" => false,
        ], $content['actualizacion']);
    }

    public function testShouldNotUpdateDisponibilidadSuccessfully(): void
    {
        $data = [
            "consultor" => "consultor@ejemplo.com",
            "fecha_ini" => "2025-04-24 15:30:00",
        ];


        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);


        $service = new DisponibilidadUpdateAdminService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("No se ha actualizado ningun dato", $content['message']);
    }



    public function testShouldInvalidDateTimeException(): void
    {
        $data = [
            "consultor" => "consultor@ejemplo.com",
            "disponible" => true,
            "fecha_ini" => "2025-04-2423 15:30:00",
            "fecha_fin" => "2025-05-12 15:30:00"
        ];
        $this->expectException(InvalidDateTimeException::class);
        $service = new DisponibilidadUpdateAdminService($this->disponibilidadRepository, $this->consultorRepository);
        $service($data);
    }

    public function testShouldInvalidDateRangeException(): void
    {
        $data = [
            "consultor" => "consultor@ejemplo.com",
            "disponible" => true,
            "fecha_ini" => "2025-04-24 15:30:00",
            "fecha_fin" => "2025-02-12 15:30:00"
        ];
        $disponibilidad = $this->createMockDisponibilidad();
        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->willReturn($disponibilidad);
        $this->expectException(InvalidDateRangeExcpecion::class);
        $service = new DisponibilidadUpdateAdminService($this->disponibilidadRepository, $this->consultorRepository);
        $service($data);
    }
}