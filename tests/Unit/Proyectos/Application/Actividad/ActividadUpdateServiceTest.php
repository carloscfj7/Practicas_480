<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Services\Actividad\ActividadUpdateService;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ActividadUpdateServiceTest extends Unit
{

    private ActividadRepositoryInterface $actividadRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private ProyectoRepositoryInterface $proyectoRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actividadRepository = $this->createMock(ActividadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
    }

    private function createMockActividad()
    {
        return $this->createMock(Actividad::class);
    }

    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }

    private function createMockUsuario()
    {
        $email = new Email('consultor@prueba.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);
        return $usuario;
    }

    private function createMockProyecto()
    {
        return $this->createMock(Proyecto::class);
    }

    public function testShouldUpdateAllActividadSuccessfully(): void
    {
        $data = [
            "nombre" => "Actividad",
            "proyecto" => "Proyectos ejemplo",
            "descripcion" => "descripcion de ejemplo"
        ];

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $actividad = $this->createMockActividad();

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombreAndConsultor')
            ->with($data['proyecto'], $consultor)
            ->willReturn($proyecto);

        $this->actividadRepository
            ->expects($this->once())
            ->method('validateActividadByNombreProyectoAndUsuario')
            ->with($data['nombre'], $proyecto ,$usuario)
            ->willReturn($actividad);

        $actividad
            ->expects($this->once())
            ->method('setDescripcion');

        $service = new ActividadUpdateService($this->actividadRepository, $this->proyectoRepository, $this->consultorRepository);
        $response = $service($data, $usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizado', $content);

        $this->assertEquals("descripcion de ejemplo", $content['actualizado']);
    }
    public function testShouldNotUpdateHabilidadSuccessfully(): void
    {
        $data = [
            "nombre" => "Actividad",
            "proyecto" => "Proyectos ejemplo",
        ];

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $actividad = $this->createMockActividad();

        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombreAndConsultor')
            ->with($data['proyecto'], $consultor)
            ->willReturn($proyecto);

        $this->actividadRepository
            ->expects($this->once())
            ->method('validateActividadByNombreProyectoAndUsuario')
            ->with($data['nombre'], $proyecto ,$usuario)
            ->willReturn($actividad);

        $service = new ActividadUpdateService($this->actividadRepository, $this->proyectoRepository, $this->consultorRepository);
        $response = $service($data, $usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("No se ha actualizado ninguna informacion", $content['message']);
    }


}