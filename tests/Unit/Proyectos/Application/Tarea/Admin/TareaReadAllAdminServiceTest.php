<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Admin;

use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Admin\TareaReadAllAdminService;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadAllAdminServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private TareaDto $tareaDto;
    private TareaReadAllAdminService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadAllAdminService(
            $this->tareaRepository,
            $this->tareaDto
        );
    }

    public function testShouldReturnEmptyTareasMessage(): void
    {
        $this->tareaRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $response = ($this->service)();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("No se encontraron tareas", $content['message']);
    }

    public function testShouldReturnTareasSuccessfully(): void
    {
        $tareaMock = $this->createMock(Tarea::class);
        $tareaDtoArray = [['nombre' => 'Tarea 1']];

        $this->tareaRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$tareaMock]);

        $this->tareaDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with([$tareaMock])
            ->willReturn($tareaDtoArray);

        $response = ($this->service)();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("Estas son todas las tareas", $content['message']);
        $this->assertEquals($tareaDtoArray, $content['tareas']);
    }
}
