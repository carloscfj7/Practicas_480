<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos;

use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Services\Proyectos\ProyectoUpdateService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\ValueObjects\Estado;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoUpdateServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
    }

    private function createMockProyecto()
    {
        $proyecto = $this->createMock(Proyecto::class);
        $proyecto->method('getDescripcion')->willReturn('Vieja descripción');
        $proyecto->method('getFechaIni')->willReturn(new \DateTime('2024-01-01'));
        $proyecto->method('getFechaFin')->willReturn(new \DateTime('2024-12-31'));
        $proyecto->method('getEstado')->willReturn(Estado::from('en proceso'));
        return $proyecto;
    }

    public function testShouldUpdateProyectoSuccessfully(): void
    {
        $data = [
            'nombre' => 'Proyectos Prueba',
            'proyecto' => 'Proyectos Prueba',
            'descripcion' => 'Nueva descripción',
            'fecha_fin' => '2025-01-01',
            'estado' => 'completado',
        ];

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $proyecto
            ->expects($this->once())
            ->method('setDescripcion')
            ->with('Nueva descripción');

        $proyecto
            ->expects($this->once())
            ->method('setFechaFin');

        $proyecto
            ->expects($this->once())
            ->method('setEstado');

        $this->proyectoRepository
            ->expects($this->once())
            ->method('save');

        $service = new ProyectoUpdateService($this->proyectoRepository, $this->consultorRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Datos actualizados correctamente', $content['message']);
        $this->assertArrayHasKey('actualizacion', $content);
    }

    public function testShouldNotUpdateAnything(): void
    {
        $data = [
            'nombre' => 'Proyectos Prueba',
            'proyecto' => 'Proyectos Prueba',
        ];

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->proyectoRepository
            ->expects($this->never())
            ->method('save');

        $service = new ProyectoUpdateService($this->proyectoRepository, $this->consultorRepository);
        $response = $service($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("No se ha insertado ningún valor que se pueda modificar o los valores insertados son iguales que los actuales", $content['error']);
    }


}
