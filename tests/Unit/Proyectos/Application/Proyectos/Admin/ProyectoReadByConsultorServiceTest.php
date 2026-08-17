<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Admin;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Admin\ProyectoReadByConsultorService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadByConsultorServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private ProyectoDto $proyectoDto;

    protected function setUp(): void
    {
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->proyectoDto = $this->createMock(ProyectoDto::class);
    }

    private function mockConsultor(): Consultor
    {
        return $this->createMock(Consultor::class);
    }

    private function mockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldReadProyectosSuccessfully(): void
    {
        $email = 'consultor@test.com';
        $consultor = $this->mockConsultor();
        $proyectos = [$this->mockProyecto()];
        $dtoArray = [['nombre' => 'Proyecto 1']];

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($email)
            ->willReturn($consultor);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('getProyectosByConsultor')
            ->with($consultor)
            ->willReturn($proyectos);

        $this->proyectoDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($proyectos)
            ->willReturn($dtoArray);

        $service = new ProyectoReadByConsultorService(
            $this->proyectoRepository,
            $this->consultorRepository,
            $this->proyectoDto
        );

        $response = $service(['email' => $email]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals("Estos todos los proyectos del consultor con email: $email", $content['message']);
        $this->assertEquals($dtoArray, $content['proyectos']);
    }

    public function testShouldNotReadAnyProyecto(): void
    {
        $email = 'consultor@test.com';
        $consultor = $this->mockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($email)
            ->willReturn($consultor);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('getProyectosByConsultor')
            ->with($consultor)
            ->willReturn([]);

        $this->proyectoDto
            ->expects($this->never())
            ->method('collectionFromEntities');

        $service = new ProyectoReadByConsultorService(
            $this->proyectoRepository,
            $this->consultorRepository,
            $this->proyectoDto
        );

        $response = $service(['email' => $email]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals("El consultor $email no tiene ningun proyecto", $content['message']);
        $this->assertArrayNotHasKey('proyectos', $content);
    }

}