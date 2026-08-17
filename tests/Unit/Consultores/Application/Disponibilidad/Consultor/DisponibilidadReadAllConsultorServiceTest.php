<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Entity\DisponibilidadDto;
use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadReadAllConsultorService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\Email;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadReadAllConsultorServiceTest extends Unit
{

    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private DisponibilidadDto $disponibilidadDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->disponibilidadDto = $this->createMock(DisponibilidadDto::class);
    }


    private function createMockUsuario()
    {
        $email = new Email('consultor@prueba.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);
        return $usuario;
    }

    private function createMockDisponibilidad()
    {
        $disponibilidad = $this->createMock(Disponibilidad::class);
        $disponibilidad->method('isDisponible')->willReturn(true);
        $fecha_fin = \DateTime::createFromFormat('Y-m-d H:i:s', "2025-05-12 15:30:00");
        $disponibilidad->method('getFechaFin')->willReturn($fecha_fin);
        return $disponibilidad;

    }

    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }
    public function testShouldReadAllDisponibilidadByConsultorSuccessfully(): void
    {

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $disponibilidades = [$this->createMockDisponibilidad()];

        $formatedDisponibilidades = [
            ['disponible' => true, 'fecha_ini' => "2025-05-12 15:30:00"],
            ['disponible' => false, 'fecha_ini' => "2025-05-13 15:30:00"]
        ];

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('findByConsultor')
            ->with($consultor)
            ->willReturn($disponibilidades);

        $this->disponibilidadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($disponibilidades)
            ->willReturn($formatedDisponibilidades);

        $service = new DisponibilidadReadAllConsultorService($this->disponibilidadRepository, $this->consultorRepository, $this->disponibilidadDto);
        $response = $service($usuario);

        $this->assertEquals($formatedDisponibilidades, $response);
    }

    public function testShouldReadNoDisponibilidadService():void
    {

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('findByConsultor')
            ->willReturn([]);

        $service = new DisponibilidadReadAllConsultorService($this->disponibilidadRepository, $this->consultorRepository, $this->disponibilidadDto);
        $response = $service($usuario);

        $this->assertEquals(null, $response);
    }


}