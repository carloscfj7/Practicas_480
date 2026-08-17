<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Admin;

use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadAllService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadAllServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ProyectoDto $proyectoDto;

    protected function setUp(): void
    {
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->proyectoDto = $this->createMock(ProyectoDto::class);
    }

    private function mockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldReadAllProyectosSuccessfully(): void
    {
        $proyectos = [$this->mockProyecto()];
        $dtoArray = [['nombre' => 'Proyecto A']];

        $this->proyectoRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($proyectos);

        $this->proyectoDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($proyectos)
            ->willReturn($dtoArray);

        $service = new ProyectoReadAllService($this->proyectoRepository, $this->proyectoDto);
        $response = $service();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Estos todos los proyectos', $content['message']);
        $this->assertEquals($dtoArray, $content['proyectos']);
    }

    public function testShouldNotReadAnyProyecto(): void
    {
        $this->proyectoRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $this->proyectoDto
            ->expects($this->never())
            ->method('collectionFromEntities');

        $service = new ProyectoReadAllService($this->proyectoRepository, $this->proyectoDto);
        $response = $service();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('No hay ningun proyecto', $content['message']);
        $this->assertArrayNotHasKey('proyectos', $content);
    }
}
