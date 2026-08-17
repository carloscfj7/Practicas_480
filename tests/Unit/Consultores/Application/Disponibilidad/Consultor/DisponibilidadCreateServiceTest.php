<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Consultor;

use App\Consultores\Application\Dto\Request\Disponibilidad\DisponibilidadConsultorRequestDto;
use App\Consultores\Application\Services\Disponibilidad\Consultor\DisponibilidadCreateService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;

class DisponibilidadCreateServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
    }

    private function createMockUsuario()
    {
        $email = new Email('consultor@prueba.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);
        return $usuario;
    }

    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }

    public function testShouldCreateDisponibildiadSucesfully(): void
    {
        $data = new DisponibilidadConsultorRequestDto(fecha_ini: '2025-04-24 15:30:00',
            fecha_fin: '2025-05-12 15:30:00',
            disponible: true);
        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data->fecha_ini);

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateExistentDisponibilidad')
            ->with($consultor, $fecha_ini);
        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('save');
        $service = new DisponibilidadCreateService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($usuario,$data);

        $this->assertEquals('Disponibilidad creada correctamente'
            , $response->message);
    }

    public function testShouldInvalidDateTimeException(): void
    {
        $data = new DisponibilidadConsultorRequestDto(fecha_ini: '2025-04-2423 15:30:00',
            fecha_fin: '2025-05-12 15:30:00',
            disponible: true);
        $this->expectException(InvalidDateTimeException::class);
        $service = new DisponibilidadCreateService($this->disponibilidadRepository, $this->consultorRepository);
        $service($this->createMockUsuario(), $data);
    }

    public function testShouldInvalidDateRangeException(): void
    {
        $data = new DisponibilidadConsultorRequestDto(fecha_ini: '2025-04-24 15:30:00',
            fecha_fin: '2025-02-12 15:30:00',
            disponible: true);
        $this->expectException(InvalidDateRangeExcpecion::class);
        $service = new DisponibilidadCreateService($this->disponibilidadRepository, $this->consultorRepository);
        $service($this->createMockUsuario(), $data);
    }
}