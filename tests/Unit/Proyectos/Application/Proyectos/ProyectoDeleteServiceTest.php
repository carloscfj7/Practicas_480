<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos;

use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Application\Services\Proyectos\ProyectoDeleteService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoDeleteServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
    }

    private function createMockProyecto()
    {
        $proyecto = $this->createMock(Proyecto::class);
        $proyecto->method('getNombre')->willReturn('Proyectos Test');
        return $proyecto;
    }

    public function testShouldDeleteProyectoSuccessfully(): void
    {
        $data = ['nombre' => 'Proyectos Test'];

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['nombre'])
            ->willReturn($proyecto);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('remove')
            ->with($proyecto);

        $service = new ProyectoDeleteService($this->proyectoRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("Proyecto eliminado correctamente", $content['message']);
        $this->assertEquals("Proyectos Test", $content['nombre']);
    }



}
