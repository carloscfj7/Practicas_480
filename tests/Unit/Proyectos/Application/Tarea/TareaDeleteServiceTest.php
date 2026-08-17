<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea;

use App\Proyectos\Application\Exceptions\Tarea\TareaNotFoundException;
use App\Proyectos\Application\Services\Tareas\TareaDeleteService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaDeleteServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private TareaDeleteService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);

        $this->service = new TareaDeleteService(
            $this->tareaRepository,
            $this->proyectoRepository
        );
    }

    private function createMockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    private function createMockTarea(): Tarea
    {
        return $this->createMock(Tarea::class);
    }




    public function testShouldDeleteTareaSuccessfully(): void
    {
        $tarea = $this->createMockTarea();
        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateTareaByProyectoAndNombre')
            ->willReturn($tarea);

        $this->tareaRepository
            ->expects($this->once())
            ->method('remove')
            ->with($tarea);

        $response = ($this->service)([
            'nombre' => 'Tarea 1',
            'proyecto' => 'Proyecto X',
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Tarea eliminada correctamente', $content['message']);
    }
}
