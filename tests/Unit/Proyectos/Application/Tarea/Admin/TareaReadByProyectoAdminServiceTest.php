<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Admin;

use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Admin\TareaReadByProyectoAdminService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoAdminServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private TareaDto $tareaDto;
    private TareaReadByProyectoAdminService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadByProyectoAdminService(
            $this->tareaRepository,
            $this->proyectoRepository,
            $this->tareaDto
        );
    }

    private function createMockProyecto(string $nombre = 'Proyecto Test'): Proyecto
    {
        $proyecto = $this->createMock(Proyecto::class);
        $proyecto->method('getNombre')->willReturn($nombre);
        return $proyecto;
    }

    private function createMockTarea(): Tarea
    {
        return $this->createMock(Tarea::class);
    }



    public function testShouldNotReadAnyTareasByProyecto(): void
    {
        $data = ['proyecto' => 'Proyecto X'];
        $proyecto = $this->createMockProyecto('Proyecto X');

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByProyecto')
            ->with($proyecto)
            ->willReturn([]);

        $response = ($this->service)($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('no tiene ninguna tarea', $content['message']);
    }

    public function testShouldReadTareasByProyectoSuccessfully(): void
    {
        $data = ['proyecto' => 'Proyecto X'];
        $proyecto = $this->createMockProyecto('Proyecto X');
        $tarea = $this->createMockTarea();
        $tareasDto = [['nombre' => 'Tarea 1']];

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByProyecto')
            ->with($proyecto)
            ->willReturn([$tarea]);

        $this->tareaDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with([$tarea])
            ->willReturn($tareasDto);

        $response = ($this->service)($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son las tareas del proyecto: Proyecto X', $content['message']);
        $this->assertEquals($tareasDto, $content['tareas']);
    }
}
