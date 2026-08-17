<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Services\Tareas\TareaCreateService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaCreateServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private TareaCreateService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);

        $this->service = new TareaCreateService(
            $this->tareaRepository,
            $this->consultorRepository,
            $this->proyectoRepository
        );
    }

    private function createMockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    private function createMockConsultor(): Consultor
    {
        return $this->createMock(Consultor::class);
    }



    public function testShouldThrowInvalidDateTimeException(): void
    {
        $this->expectException(InvalidDateTimeException::class);

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateExistentTarea');

        ($this->service)([
            'nombre' => 'Tarea',
            'proyecto' => 'Proyecto',
            'descripcion' => 'Descripción',
            'estado' => 'pendiente',
            'fecha_ini' => 'fecha_mal',
            'fecha_fin' => '2025-06-01 10:00:00',
            'consultores' => ['consultor@test.com'],
        ]);
    }

    public function testShouldThrowInvalidDateRangeException(): void
    {
        $this->expectException(InvalidDateRangeExcpecion::class);

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateExistentTarea');

        ($this->service)([
            'nombre' => 'Tarea',
            'proyecto' => 'Proyecto',
            'descripcion' => 'desc',
            'estado' => 'pendiente',
            'fecha_ini' => '2025-06-10 10:00:00',
            'fecha_fin' => '2025-06-01 10:00:00',
            'consultores' => ['consultor@test.com'],
        ]);
    }


    public function testShouldCreateTareaSuccessfully(): void
    {
        $proyecto = $this->createMockProyecto();
        $consultor = $this->createMockConsultor();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->any())
            ->method('validateExistentTarea');

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->isInstanceOf(Tarea::class));

        $response = ($this->service)([
            'nombre' => 'Nueva tarea',
            'proyecto' => 'Proyecto X',
            'descripcion' => 'Descripción nueva',
            'estado' => 'pendiente',
            'fecha_ini' => '2025-06-01 10:00:00',
            'fecha_fin' => '2025-06-10 10:00:00',
            'consultores' => ['consultor@test.com'],
        ]);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('Tarea creada correctamente', $content['message']);
        $this->assertArrayHasKey('nombre', $content);
        $this->assertEquals('Nueva tarea', $content['nombre']);
    }
}
