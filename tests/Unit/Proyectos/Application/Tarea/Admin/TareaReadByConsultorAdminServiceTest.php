<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Admin;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Admin\TareaReadByConsultorAdminService;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByConsultorAdminServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private TareaDto $tareaDto;
    private TareaReadByConsultorAdminService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadByConsultorAdminService(
            $this->tareaRepository,
            $this->consultorRepository,
            $this->tareaDto
        );
    }



    public function testShouldNotReadAnyTareaByConsultor(): void
    {
        $data = ['consultor' => 'email@test.com'];
        $consultor = $this->createMock(Consultor::class);
        $consultor->method('getNombre')->willReturn('Juan Pérez');

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByConsultor')
            ->with($consultor)
            ->willReturn([]);

        $response = ($this->service)($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("No se encontraron tareas para el consultor Juan Pérez", $content['message']);
    }

    public function testShouldReturnTareasByConsultorSuccessfully(): void
    {
        $data = ['consultor' => 'email@test.com'];
        $consultor = $this->createMock(Consultor::class);
        $tareaMock = $this->createMock(Tarea::class);
        $tareaDtoArray = [['nombre' => 'Tarea 1']];

        $consultor->method('getNombre')->willReturn('Juan Pérez');

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByConsultor')
            ->with($consultor)
            ->willReturn([$tareaMock]);

        $this->tareaDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with([$tareaMock])
            ->willReturn($tareaDtoArray);

        $response = ($this->service)($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("Estas son las tearas del consultor con email: email@test.com", $content['message']);
        $this->assertEquals($tareaDtoArray, $content['tareas']);
    }
}
