<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Services\Actividad\ActividadCreateService;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class  ActividadCreateServiceTest extends Unit
{
    private ActividadRepositoryInterface $actividadRepository;
    private ProyectoRepositoryInterface $proyectoRepository;

    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actividadRepository = $this->createMock(ActividadRepositoryInterface::class);
        $this->proyectoRepository = $this->createMock(ProyectoRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
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
            "fecha" => "2025-03-10",
            "descripcion" => "descripcion 1",
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


        $this->actividadRepository
            ->expects($this->once())
            ->method('validateExistentActividad')
            ->with($data['nombre'], $proyecto, $usuario);

        $this->actividadRepository
            ->expects($this->once())
            ->method('save');



        $service = new ActividadCreateService($this->actividadRepository, $this->proyectoRepository, $this->consultorRepository);
        $response = $service($data, $usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertEquals('La actividad se ha creado correctamente para el proyecto: ', $content['message']);
    }



    public function testShouldThrowRequiredInvalidDateException(): void
    {
        $data = [
            "nombre" => "Actividad 4",
            "fecha" => "20252-03-10",
            "descripcion" => "descripcion 1",
            "proyecto" => "Proyectos ejemplo"
        ];
        $usuario = $this->createMockUsuario();
        $this->expectException(InvalidDateException::class);
        $service = new ActividadCreateService($this->actividadRepository, $this->proyectoRepository, $this->consultorRepository);
        $service($data, $usuario);
    }

}