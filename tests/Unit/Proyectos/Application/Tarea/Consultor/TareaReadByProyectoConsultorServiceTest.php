<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Consultor;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Consultor\TareaReadByProyectoConsultorService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoConsultorServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private TareaDto $tareaDto;
    private TareaReadByProyectoConsultorService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadByProyectoConsultorService(
            $this->tareaRepository,
            $this->proyectoRepository,
            $this->consultorRepository,
            $this->tareaDto
        );
    }

    private function createMockUsuario(string $email): Usuario
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email($email));
        return $usuario;
    }

    private function createMockConsultor(): Consultor
    {
        return $this->createMock(Consultor::class);
    }

    private function createMockProyecto(string $nombre = 'Proyecto'): Proyecto
    {
        $proyecto = $this->createMock(Proyecto::class);
        $proyecto->method('getNombre')->willReturn($nombre);
        return $proyecto;
    }

    private function createMockTarea(): Tarea
    {
        return $this->createMock(Tarea::class);
    }





    public function testShouldNotReadAnyTareaBYProyecto(): void
    {
        $usuario = $this->createMockUsuario('consultor@example.com');
        $consultor = $this->createMockConsultor();
        $proyecto = $this->createMockProyecto();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->willReturn($consultor);
        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($proyecto);
        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByProyectoAndConsultor')
            ->willReturn([]);

        $response = ($this->service)(['proyecto' => 'Proyecto'], $usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('No se ha encontrado ninguna tarea del consultor', $content['message']);
    }

    public function testShouldReadTareasByProyectoSuccessfully(): void
    {
        $usuario = $this->createMockUsuario('consultor@example.com');
        $consultor = $this->createMockConsultor();
        $proyecto = $this->createMockProyecto();

        $tareas = [$this->createMockTarea()];
        $tareasDto = [['nombre' => 'Tarea 1']];

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->willReturn($consultor);
        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->willReturn($proyecto);
        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByProyectoAndConsultor')
            ->willReturn($tareas);
        $this->tareaDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->willReturn($tareasDto);

        $response = ($this->service)(['proyecto' => 'Proyecto'], $usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las tareas del consultor con email consultor@example.com en el proyecto: Proyecto', $content['message']);
        $this->assertArrayHasKey('tareas', $content);
    }
}
