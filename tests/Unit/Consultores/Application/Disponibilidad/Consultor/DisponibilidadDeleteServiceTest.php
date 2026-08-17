<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Consultor;

use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibildiadDeleteService;
use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadCreateService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;

class DisponibilidadDeleteServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
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

    public function testShouldDeleteDisponibildiadSucesfully(): void
    {
        $data = [
            "fecha_ini" => "2025-04-24 15:30:00",
        ];

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $disponibilidad = $this->createMockDisponibilidad();
        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('remove');
        $service = new DisponibildiadDeleteService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($usuario,$data);

        $this->assertEquals("Disponibilidad eliminada correctamente", $response);
    }

}