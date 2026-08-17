<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Proyectos\Consultor;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\ProyectoDto;
use App\Proyectos\Application\Services\Proyectos\Consultor\ProyectoReadOneConsultorService;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ProyectoReadOneConsultorServiceTest extends Unit
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
        $emailValueObject = $this->createMock(Email::class);

        $emailValueObject
            ->method('value')
            ->willReturn($email);

        $usuario
            ->method('getEmail')
            ->willReturn($emailValueObject);

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
    public function testShouldReadProyectoByConsultorSuccessfully(): void
    {
        $data = ['nombre' => 'Proyecto Uno'];
        $usuario = $this->mockUsuario('consultor@test.com');
        $consultor = $this->mockConsultor();
        $proyecto = $this->mockProyecto();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with('consultor@test.com')
            ->willReturn($consultor);

        $this->proyectoRepository
            ->expects($this->once())
            ->method('validateProyectoByNombreAndConsultor')
            ->with($data['nombre'], $consultor)
            ->willReturn($proyecto);

        $this->proyectoDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($proyecto)
            ->willReturn($this->proyectoDto);

        $service = new ProyectoReadOneConsultorService($this->proyectoRepository, $this->consultorRepository, $this->proyectoDto);
        $response = $service($data, $usuario);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Estos son los datos del proyecto', $content['message']);
    }





}
