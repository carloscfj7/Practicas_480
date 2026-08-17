<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Admin;

use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Admin\TareaReadByProyectoAndNameAdminService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoAndNameAdminServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private TareaDto $tareaDto;
    private TareaReadByProyectoAndNameAdminService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadByProyectoAndNameAdminService(
            $this->tareaRepository,
            $this->proyectoRepository,
            $this->tareaDto
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



    public function testShouldReadTareaByProyectoAndNameSuccessfully(): void
    {
        $data = [
            'proyecto' => 'Proyecto 1',
            'nombre' => 'Tarea Importante'
        ];

        $proyecto = $this->createMockProyecto();
        $tarea = $this->createMockTarea();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateTareaByProyectoAndNombre')
            ->with($data['nombre'], $proyecto)
            ->willReturn($tarea);

        $this->tareaDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($tarea)
            ->willReturn($this->tareaDto);

        $response = ($this->service)($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estos son los datos de la tarea', $content['message']);
        $this->assertArrayHasKey('tarea', $content);
    }
}
