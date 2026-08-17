<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadReadAllAdminService;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadAllAdminServiceTest extends Unit
{

    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private DisponibilidadDto $disponibilidadDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
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

    public function testShouldReadAllDisponibilidadSuccessfully(): void
    {

        $disponibilidades = [$this->createMockDisponibilidad()];

        $formatedDisponibilidades = [
            ['disponible' => true, 'fecha_ini' => "2025-05-12 15:30:00"],
            ['disponible' => false, 'fecha_ini' => "2025-05-13 15:30:00"]
        ];

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('getALl')
            ->willReturn($disponibilidades);

        $this->disponibilidadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($disponibilidades)
            ->willReturn($formatedDisponibilidades);

        $service = new DisponibilidadReadAllAdminService($this->disponibilidadRepository, $this->disponibilidadDto);
        $response = $service();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("Estas son todas las disponibilidades: ", $content['message']);
        $this->assertEquals($formatedDisponibilidades, $content['disponibilidad']);
    }

    public function testShouldReadNoDisponibilidadService():void
    {

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('getALl')
            ->willReturn([]);


        $service = new DisponibilidadReadAllAdminService($this->disponibilidadRepository, $this->disponibilidadDto);
        $response = $service();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("No existen disponibilidades", $content['message']);
    }


}