<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadConsultorRequestDto;
use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadUpdateService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Disponibilidad;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;

class DisponibilidadUpdateServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    private DisponibilidadUpdateService $disponibilidadUpdateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->disponibilidadUpdateService = new DisponibilidadUpdateService($this->disponibilidadRepository, $this->consultorRepository);
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

    public function testShouldUpdateAllDisponibilidadSuccessfully(): void
    {
        $data = new DisponibilidadConsultorRequestDto("2025-04-24 15:30:00", "2025-05-14 15:30:00", false);


        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data->fecha_ini);;

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);


        $disponibilidad
            ->expects($this->once())
            ->method('setFechaFin');


        $response = ($this->disponibilidadUpdateService)($usuario, $data);


        $this->assertEquals("2025-05-14 15:30:00", $response->actualizacion['fecha_fin']);
    }

    public function testShouldUpdateFechaFinDisponibilidadSuccessfully(): void
    {
        $data = new DisponibilidadConsultorRequestDto("2025-04-24 15:30:00", "2025-05-14 15:30:00");

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data->fecha_ini);

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);

        $disponibilidad
            ->expects($this->once())
            ->method('setFechaFin');


        $response = ($this->disponibilidadUpdateService)($usuario, $data);


        $this->assertEquals("2025-05-14 15:30:00", $response->actualizacion['fecha_fin']);
    }

    public function testShouldNotUpdateDisponibilidadSuccessfully(): void
    {
        $data = new DisponibilidadConsultorRequestDto("2025-04-24 15:30:00");
        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data->fecha_ini);

        $disponibilidad = $this->createMockDisponibilidad();

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->with($consultor, $fecha_ini)
            ->willReturn($disponibilidad);


        $response = ($this->disponibilidadUpdateService)($usuario, $data);

        $this->assertEquals("No se ha actualizado ningun dato", $response->message);
    }

    public function testShouldInvalidDateTimeException(): void
    {
        $data = new DisponibilidadConsultorRequestDto(fecha_ini: "2025-04-2423 15:30:00",
            fecha_fin: "2025-05-12 15:30:00",
            disponible: true);
        $this->expectException(InvalidDateTimeException::class);
        $service = new DisponibilidadUpdateService($this->disponibilidadRepository, $this->consultorRepository);
        $service($this->createMockUsuario(), $data);
    }

    public function testShouldInvalidDateRangeException(): void
    {
        $data = new DisponibilidadConsultorRequestDto(fecha_ini: "2025-04-24 15:30:00",
            fecha_fin: "2025-02-12 15:30:00",
            disponible: true);

        $disponibilidad = $this->createMockDisponibilidad();
        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateDisponibilidad')
            ->willReturn($disponibilidad);


        $this->expectException(InvalidDateRangeExcpecion::class);
        $service = new DisponibilidadUpdateService($this->disponibilidadRepository, $this->consultorRepository);
        $service($this->createMockUsuario(), $data);
    }
}