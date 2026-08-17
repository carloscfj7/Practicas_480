<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Consultor;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Consultor\TareaReadOneConsultorService;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadOneConsultorServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private TareaDto $tareaDto;
    private TareaReadOneConsultorService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadOneConsultorService(
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

    private function createMockTarea()
    {
        return $this->createMock(Tarea::class);
    }

    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }





    public function testShouldReadTareaByConsultorDetailsSuccessfully(): void
    {
        $data = ['nombre' => 'Tarea 1'];
        $usuario = $this->createMockUsuario('consultor@example.com');

        $tarea = $this->createMockTarea();

        $this->tareaDto->method('fromEntity')->willReturn($this->tareaDto);

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateTareaByConsultorAndNombre')
            ->with($data['nombre'], $consultor)
            ->willReturn($tarea);

        $response = ($this->service)($data, $usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estos son los datos de la tarea', $content['message']);
        $this->assertArrayHasKey('tarea', $content);
    }
}
