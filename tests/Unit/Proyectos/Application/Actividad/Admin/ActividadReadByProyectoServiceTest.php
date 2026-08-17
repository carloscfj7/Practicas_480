<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad\Admin;


use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadByProyectoService;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadByProyectoServiceTest extends Unit
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

    public function testShouldReadActividadByProyectoSuccessfully(): void
    {
        $data = [
            "proyecto"=> "proyecto ejemplo"
        ];


        $actividades = [$this->createMockActividad()];

        $formattedActividades = [
            ['nombre' => 'actividad1', 'descripcion' => 'descripcion 1']
        ];

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->actividadRepository
            ->expects($this->once())
            ->method('findByProyecto')
            ->with($proyecto)
            ->willReturn($actividades);

        $this->actividadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($actividades)
            ->willReturn($formattedActividades);

        $service = new ActividadReadByProyectoService($this->actividadRepository, $this->proyectoRepository, $this->actividadDto);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('Estas son la actividades registradas en el proyecto: ', $content['message']);
        $this->assertEquals($formattedActividades, $content['actividades']);
    }

    public function testShouldNotReadAnyActividadByProyectoSuccessfully(): void
    {
        $data = [
            "proyecto"=> "proyecto ejemplo"
        ];



        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->actividadRepository
            ->expects($this->once())
            ->method('findByProyecto')
            ->with($proyecto)
            ->willReturn([]);


        $service = new ActividadReadByProyectoService($this->actividadRepository, $this->proyectoRepository, $this->actividadDto);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('El proyecto  no tiene actividades', $content['message']);
    }




}