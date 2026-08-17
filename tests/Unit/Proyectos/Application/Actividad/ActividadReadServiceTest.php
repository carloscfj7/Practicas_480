<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Application\Services\Actividad\ActividadReadService;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadServiceTest extends Unit
{
    private ActividadRepositoryInterface $actividadRepository;
    private ProyectoRepositoryInterface $proyectoRepository;

    private ConsultorRepositoryInterface $consultorRepository;
    private ActividadDto $actividadDto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actividadRepository = $this->createMock(ActividadRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->actividadDto = $this->createMock(ActividadDto::class);
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

    public function testShouldReadActividadSuccessfully(): void
    {
        $data = [
            "nombre" => "Actividad 4",
            "proyecto" => "Proyectos ejemplo"
        ];

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);


        $proyecto = $this->createMockProyecto();

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombreAndConsultor')
            ->with($data['proyecto'], $consultor)
            ->willReturn($proyecto);

        $actividad = $this->createMockActividad();

        $this->actividadRepository
            ->expects($this->once())
            ->method('validateActividadByNombreProyectoAndUsuario')
            ->with($data['nombre'], $proyecto, $usuario)
            ->willReturn($actividad);

        $this->actividadDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($actividad)
            ->willReturn($this->actividadDto);

        $service = new ActividadReadService($this->actividadRepository, $this->proyectoRepository, $this->consultorRepository, $this->actividadDto);
        $response = $service($data,$usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("Estos son los datos de la actividad: ", $content['message']);
    }



}