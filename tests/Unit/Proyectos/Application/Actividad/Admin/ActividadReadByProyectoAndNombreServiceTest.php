<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad\Admin;


use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadByProyectoAndNombreService;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadByProyectoAndNombreServiceTest extends Unit
{
    private ActividadRepositoryInterface $actividadRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private ActividadDto $actividadDto;


    protected function setUp(): void
    {
        parent::setUp();

        $this->actividadRepository = $this->createMock(ActividadRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->actividadDto = $this->createMock(ActividadDto::class);
    }

    private function createMockActividad()
    {
        return $this->createMock(Actividad::class);
    }


    private function createMockProyecto()
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldReadActividadByProyectoAndNombreSuccessfully(): void
    {
        $data = [
            "nombre"=> "Actividad 4",
            "proyecto"=> "proyecto ejemplo"
        ];


        $actividades = $this->createMockActividad();


        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->actividadRepository
            ->expects($this->once())
            ->method('validateActividadByNombreAndProyecto')
            ->with($data['nombre'],$proyecto)
            ->willReturn($actividades);

        $this->actividadDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($actividades)
            ->willReturn($this->actividadDto);

        $service = new ActividadReadByProyectoAndNombreService($this->actividadRepository, $this->proyectoRepository, $this->actividadDto);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('Estos son los datos de la actividad: ', $content['message']);
    }




}