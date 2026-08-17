<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Consultor;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Consultor\TareaReadByProyectoAndNameConsultorService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoAndNameServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ProyectoRepositoryInterface $proyectoRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private TareaDto $tareaDto;
    private TareaReadByProyectoAndNameConsultorService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadByProyectoAndNameConsultorService(
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

    private function createMockProyecto(): Proyecto
    {
        return $this->createMock(Proyecto::class);
    }

    private function createMockTarea(): Tarea
    {
        return $this->createMock(Tarea::class);
    }



    public function testShouldReadTareaByProyectoSuccessfully(): void
    {
        $data = ['nombre' => 'Tarea Importante', 'proyecto' => 'Proyecto X'];
        $usuario = $this->createMockUsuario('consultor@example.com');
        $email = $usuario->getEmail()->value();

        $consultor = $this->createMockConsultor();
        $proyecto = $this->createMockProyecto();
        $tarea = $this->createMockTarea();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($email)
            ->willReturn($consultor);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombre')
            ->with($data['proyecto'])
            ->willReturn($proyecto);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateTareaByConsultorNombreAndProyecto')
            ->with($data['nombre'], $consultor, $proyecto)
            ->willReturn($tarea);

        $this->tareaDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($tarea)
            ->willReturn($this->tareaDto);

        $response = ($this->service)($data, $usuario);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estos son los datos de la tarea', $content['message']);
        $this->assertArrayHasKey('tarea', $content);
    }
}
