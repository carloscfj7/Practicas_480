<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Admin;

use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadOneService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadOneServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ProyectoDto $proyectoDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->proyectoDto = $this->createMock(ProyectoDto::class);
    }

    private function mockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldReadProyectoSuccessfully(): void
    {
        $nombre = 'ProyectoTest';
        $proyecto = $this->mockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($nombre)
            ->willReturn($proyecto);

        $this->proyectoDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($proyecto)
            ->willReturn($this->proyectoDto);

        $service = new ProyectoReadOneService(
            $this->proyectoRepository,
            $this->proyectoDto
        );

        $response = $service(['nombre' => $nombre]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Estos son los datos del proyecto', $content['message']);
    }


    public function testShouldThrowRerquiredDataException(): void
    {
        $this->expectException(RequiredDataException::class);

        $service = new ProyectoReadOneService(
            $this->proyectoRepository,
            $this->proyectoDto
        );

        $service([]);
    }
}
