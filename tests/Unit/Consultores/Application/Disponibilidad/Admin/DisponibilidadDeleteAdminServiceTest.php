<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadDeleteAdminService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadDeleteAdminServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    protected function setUp(): void
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

    public function testShouldDeleteDisponibildiadSucesfully(): void
    {
        $data = [
            "consultor" => "consulotr@ejemplo.com",
            "fecha_ini" => "2025-04-24 15:30:00",
        ];

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $disponibilidad = $this->createMockDisponibilidad();
        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('remove');
        $service = new DisponibilidadDeleteAdminService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("Disponibilidad eliminada", $content['message']);
    }





    public function testShouldInvalidDateTimeException(): void
    {
        $data = [
            "consultor" => "consulotr@ejemplo.com",
            "disponible" => true,
            "fecha_ini" => "2025-04-2423 15:30:00",
            "fecha_fin" => "2025-05-12 15:30:00"
        ];
        $this->expectException(InvalidDateTimeException::class);
        $service = new DisponibilidadDeleteAdminService($this->disponibilidadRepository, $this->consultorRepository);
        $service($data);
    }
}