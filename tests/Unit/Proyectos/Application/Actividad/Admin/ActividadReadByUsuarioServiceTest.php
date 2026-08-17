<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Actividad\Admin;

use App\Proyectos\Application\Dto\ActividadDto;
use App\Proyectos\Application\Services\Actividad\Admin\ActividadReadByUsuarioService;
use App\Proyectos\Domain\Actividad;
use App\Proyectos\Domain\ActividadRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\UsuarioRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ActividadReadByUsuarioServiceTest extends Unit
{
    private ActividadRepositoryInterface $actividadRepository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private ActividadDto $actividadDto;


    protected function setUp(): void
    {
        parent::setUp();

        $this->actividadRepository = $this->createMock(ActividadRepositoryInterface::class);
        $this->usuarioRepository = $this->createMock(UsuarioRepositoryInterface::class);
        $this->actividadDto = $this->createMock(ActividadDto::class);
    }

    private function createMockActividad()
    {
        return $this->createMock(Actividad::class);
    }


    private function createMockUsuario()
    {
        $email = new Email('consultor@prueba.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);
        return $usuario;
    }

    public function testShouldReadActividadByUsuarioSuccessfully(): void
    {
        $data = [
            "usuario"=> "consultor@ejemplo.com"

        ];

        $usuario = $this->createMockUsuario();

        $this->usuarioRepository
            ->expects($this->once())
            ->method('validateUsuario')
            ->with($data['usuario'])
            ->willReturn($usuario);


        $actividades = [$this->createMockActividad()];

        $formattedActividades = [
            ['nombre' => 'actividad1', 'descripcion' => 'descripcion 1']
        ];

        $this->actividadRepository
            ->expects($this->once())
            ->method('findByUsuario')
            ->with($usuario)
            ->willReturn($actividades);

        $this->actividadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($actividades)
            ->willReturn($formattedActividades);

        $service = new ActividadReadByUsuarioService($this->actividadRepository, $this->usuarioRepository, $this->actividadDto);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('Estas son las actividades del usuario con email: consultor@ejemplo.com', $content['message']);
        $this->assertEquals($formattedActividades, $content['actividades']);
    }

    public function testShouldNotReadAnyActividadByUsuarioSuccessfully(): void
    {
        $data = [
            "usuario"=> "consultor@ejemplo.com"

        ];

        $usuario = $this->createMockUsuario();

        $this->usuarioRepository
            ->expects($this->once())
            ->method('validateUsuario')
            ->with($data['usuario'])
            ->willReturn($usuario);


        $this->actividadRepository
            ->expects($this->once())
            ->method('findByUsuario')
            ->with($usuario)
            ->willReturn([]);


        $service = new ActividadReadByUsuarioService($this->actividadRepository, $this->usuarioRepository, $this->actividadDto);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals('El usuario consultor@prueba.es no tiene ninguna actividad', $content['message']);
    }




}