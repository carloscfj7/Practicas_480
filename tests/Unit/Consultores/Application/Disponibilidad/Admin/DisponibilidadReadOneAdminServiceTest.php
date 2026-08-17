<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Admin;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadReadOneAdminService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadOneAdminServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface  $consultorRepository;
    private DisponibilidadDto $disponibilidadDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->disponibilidadDto = $this->createMock(DisponibilidadDto::class);
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
    public function testShouldReadDisponibilidadSuccessfully(): void
    {
        $data = [
            "fecha_ini" => "2025-04-24 15:30:00",
            "consultor" => "consultor@ejemplo.com"
        ];


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
            ->with($consultor)
            ->willReturn($disponibilidad);

        $this->disponibilidadDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($disponibilidad)
            ->willReturn($this->disponibilidadDto);

        $service = new DisponibilidadReadOneAdminService($this->disponibilidadRepository, $this->consultorRepository, $this->disponibilidadDto);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("Estos son los datos de la disponibildiad: ", $content['message']);
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
        $service = new DisponibilidadReadOneAdminService($this->disponibilidadRepository, $this->consultorRepository, $this->disponibilidadDto);
        $service($data);
    }

}