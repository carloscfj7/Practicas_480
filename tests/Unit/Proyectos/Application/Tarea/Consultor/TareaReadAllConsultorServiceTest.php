<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Consultor;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Consultor\TareaReadAllConsultorService;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadAllConsultorServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private TareaDto $tareaDto;
    private TareaReadAllConsultorService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadAllConsultorService(
            $this->tareaRepository,
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

    private function createMockTarea(): Tarea
    {
        return $this->createMock(Tarea::class);
    }

    public function testShouldReadNoTarea(): void
    {
        $usuario = $this->createMockUsuario('consultor@example.com');
        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByConsultor')
            ->with($consultor)
            ->willReturn([]);

        $response = ($this->service)($usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('El consultor ' . $usuario->getEmail()->value() . ' no tiene ninguna tarea asociada', $content['message']);
    }

    public function testShouldReadAllTareas(): void
    {
        $usuario = $this->createMockUsuario('consultor@example.com');
        $consultor = $this->createMockConsultor();
        $tarea = $this->createMockTarea();
        $tareaDtoResult = [['nombre' => 'Tarea 1']];

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->once())
            ->method('getTareasByConsultor')
            ->with($consultor)
            ->willReturn([$tarea]);

        $this->tareaDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with([$tarea])
            ->willReturn($tareaDtoResult);

        $response = ($this->service)($usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estas son todas las tareas', $content['message']);
        $this->assertArrayHasKey('tareas', $content);
        $this->assertEquals($tareaDtoResult, $content['tareas']);
    }
}
