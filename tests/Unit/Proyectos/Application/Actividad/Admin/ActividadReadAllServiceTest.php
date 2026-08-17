<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad\Admin;


use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadAllServcie;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadAllServiceTest extends Unit
{
    private ActividadRepositoryInterface $actividadRepository;
    private ActividadDto $actividadDto;


    protected function setUp(): void
    {
        parent::setUp();

        $this->actividadRepository = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividadDto = $this->createMock(ActividadDto::class);
    }

    private function createMockActividad()
    {
        return $this->createMock(Actividad::class);
    }



    public function testShouldReadActividadSuccessfully(): void
    {

        $actividades = [$this->createMockActividad()];

        $formattedActividades = [
            ['nombre' => 'actividad1', 'descripcion' => 'descripcion 1']
        ];

        $this->actividadRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($actividades);

        $this->actividadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($actividades)
            ->willReturn($formattedActividades);

        $service = new ActividadReadAllServcie($this->actividadRepository,$this->actividadDto);
        $response = $service();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('Estas son todas las actividades: ', $content['message']);
        $this->assertEquals($formattedActividades, $content['actividades']);
    }


    public function testShouldNotReadAnyActividadSuccessfully(): void
    {

        $this->actividadRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $service = new ActividadReadAllServcie($this->actividadRepository,$this->actividadDto);
        $response = $service();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('No existe ninguna actividad', $content['message']);
    }



}