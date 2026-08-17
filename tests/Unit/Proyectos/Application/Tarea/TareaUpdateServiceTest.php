<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Services\Tareas\TareaUpdateService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaUpdateServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private TareaUpdateService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);

        $this->service = new TareaUpdateService(
            $this->tareaRepository,
            $this->consultorRepository,
            $this->proyectoRepository
        );
    }

    private function mockProyectoYtarea(Tarea $tarea): void
    {
        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($this->createMockProyecto());

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateTareaByProyectoAndNombre')
            ->willReturn($tarea);
    }

    private function createMockTarea(
        string    $descripcion = '',
        Estado    $estado = null,
        \DateTime $fechaIni = null,
        \DateTime $fechaFin = null
    )
    {
        $tarea = $this->createMock(Tarea::class);

        $tarea->method('getDescripcion')->willReturn($descripcion);
        $tarea->method('getEstado')->willReturn($estado ?? Estado::from('pendiente'));
        $tarea->method('getFechaIni')->willReturn($fechaIni ?? new \DateTime());
        $tarea->method('getFechaFin')->willReturn($fechaFin ?? new \DateTime());

        return $tarea;
    }

    private function createMockProyecto()
    {
        return $this->createMock(Proyecto::class);
    }



    public function testShouldThrowInvalidDateTimeException(): void
    {
        $this->expectException(InvalidDateTimeException::class);

        $tarea = $this->createMockTarea(fechaIni: new \DateTime('2025-05-01 10:00:00'));
        $this->mockProyectoYtarea($tarea);

        ($this->service)([
            'nombre' => 'Tarea',
            'proyecto' => 'Proyecto',
            'fecha_fin' => 'formato-invalido',
        ]);
    }

    public function testShouldThrowInvalidDateRangeException(): void
    {
        $this->expectException(InvalidDateRangeExcpecion::class);

        $tarea = $this->createMockTarea(fechaIni: new \DateTime('2025-05-10 10:00:00'));
        $this->mockProyectoYtarea($tarea);

        ($this->service)([
            'nombre' => 'Tarea',
            'proyecto' => 'Proyecto',
            'fecha_fin' => '2025-05-01 10:00:00',
        ]);
    }

    public function testShouldNotUpdate(): void
    {
        $tarea = $this->createMockTarea(
            descripcion: 'desc',
            estado: Estado::from('pendiente'),
            fechaIni: new \DateTime('2025-05-01'),
            fechaFin: new \DateTime('2025-05-10')
        );

        $this->mockProyectoYtarea($tarea);

        $response = ($this->service)([
            'nombre' => 'Tarea',
            'proyecto' => 'Proyecto',
        ]);

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('No se ha insertado ningún valor que se pueda modificar o los valores insertados son iguales que los actuales', $content['message']);
    }

    public function testShouldUpdateDescripcionEstadoFechaSuccessfully(): void
    {
        $tarea = $this->createMockTarea(
            descripcion: 'old',
            estado: Estado::from('pendiente'),
            fechaIni: new \DateTime('2025-05-01'),
            fechaFin: new \DateTime('2025-05-10')
        );

        $tarea->expects($this->once())->method('setDescripcion')->with('new');
        $tarea->expects($this->once())->method('setEstado')->with(Estado::from('en proceso'));
        $tarea->expects($this->once())->method('setFechaFin');
        $tarea->expects($this->once())->method('setEstimacion');

        $this->mockProyectoYtarea($tarea);
        $this->tareaRepository->expects($this->once())->method('save')->with($tarea);

        $response = ($this->service)([
            'nombre' => 'Tarea',
            'proyecto' => 'Proyecto',
            'descripcion' => 'new',
            'estado' => 'en proceso',
            'fecha_fin' => '2025-05-20 00:00:00',
        ]);
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Datos actualizados correctamente', $content['message']);
    }

}
