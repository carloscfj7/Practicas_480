<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Consultor;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Consultor\ProyectoReadAllConsultorService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadAllConsultorServiceTest extends Unit
{
    private ProyectoRepositoryInterface $proyectoRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private ProyectoDto $proyectoDto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->proyectoDto = $this->createMock(ProyectoDto::class);
    }

    private function mockUsuario(string $email): Usuario
    {
        $usuario = $this->createMock(Usuario::class);
        $emailValue = $this->createMock(Email::class);

        $emailValue->method('value')->willReturn($email);
        $usuario->method('getEmail')->willReturn($emailValue);

        return $usuario;
    }

    private function mockConsultor(): Consultor
    {
        return $this->createMock(Consultor::class);
    }

    private function mockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldReadAllProyectosByConsultorSuccessfully(): void
    {
        $usuario = $this->mockUsuario('consultor@test.com');
        $consultor = $this->mockConsultor();
        $proyectos = [$this->mockProyecto(), $this->mockProyecto()];
        $proyectosDto = [
            ['nombre' => 'Proyecto 1'],
            ['nombre' => 'Proyecto 2'],
        ];

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with('consultor@test.com')
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
            ->willReturn($proyectosDto);

        $service = new ProyectoReadAllConsultorService($this->proyectoRepository, $this->consultorRepository, $this->proyectoDto);
        $response = $service($usuario);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Estos son todos los proyectos del consultor con email: consultor@test.com', $content['message']);
        $this->assertEquals($proyectosDto, $content['proyectos']);
    }

    public function testShouldNotReadAnyProyectosByConsultor(): void
    {
        $usuario = $this->mockUsuario('consultor@test.com');
        $consultor = $this->mockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with('consultor@test.com')
            ->willReturn($consultor);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('getProyectosByConsultor')
            ->with($consultor)
            ->willReturn([]);

        $this->proyectoDto
            ->expects($this->never())
            ->method('collectionFromEntities');

        $service = new ProyectoReadAllConsultorService($this->proyectoRepository, $this->consultorRepository, $this->proyectoDto);
        $response = $service($usuario);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('El consultor consultor@test.com no tiene ningun proyecto asociado', $content['message']);
    }
}
