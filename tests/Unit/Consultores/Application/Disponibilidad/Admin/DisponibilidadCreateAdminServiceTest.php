<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Disponibilidad\Admin;

use App\Consultores\Application\Services\Disponibilidad\Admin\DisponibilidadCreateAdminService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\DisponibilidadRepositoryInterface;
use App\Shared\Application\Exceptions\InvalidDateTimeException;
use App\Shared\Application\Exceptions\RequiredDataException;
use App\Shared\Domain\Exceptions\InvalidDateRangeExcpecion;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class DisponibilidadCreateAdminServiceTest extends Unit
{
    private DisponibilidadRepositoryInterface $disponibilidadRepository;

    private ConsultorRepositoryInterface $consultorRepository;


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->disponibilidadRepository = $this->createMock(DisponibilidadRepositoryInterface::class);
    }



    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }

    public function testShouldCreateDisponibildiadSucesfully(): void
    {
        $data = [
            "consultor" =>'consultor@prueba.es',
            "disponible" => true,
            "fecha_ini" => "2025-04-24 15:30:00",
            "fecha_fin" => "2025-05-12 15:30:00"
        ];

        $fecha_ini = \DateTime::createFromFormat('Y-m-d H:i:s', $data['fecha_ini']);

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('validateExistentDisponibilidad')
            ->with($consultor, $fecha_ini);
        $this->disponibilidadRepository
            ->expects($this->once())
            ->method('save');
        $service = new DisponibilidadCreateAdminService($this->disponibilidadRepository, $this->consultorRepository);

        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertEquals("Disponibilidad creada correctamente para el consultor con email: consultor@prueba.es", $content['message']);
    }




    public function testShouldInvalidDateTimeException(): void
    {
        $data = [
            "consultor" =>'consultor@prueba.es',
            "disponible" => true,
            "fecha_ini" => "2025-04-2423 15:30:00",
            "fecha_fin" => "2025-05-12 15:30:00"
        ];
        $this->expectException(InvalidDateTimeException::class);
        $service = new DisponibilidadCreateAdminService($this->disponibilidadRepository, $this->consultorRepository);
        $service($data);
    }

    public function testShouldInvalidDateRangeException(): void
    {
        $data = [
            "consultor" =>'consultor@prueba.es',
            "disponible" => true,
            "fecha_ini" => "2025-04-24 15:30:00",
            "fecha_fin" => "2025-02-12 15:30:00"
        ];
        $this->expectException(InvalidDateRangeExcpecion::class);
        $service = new DisponibilidadCreateAdminService($this->disponibilidadRepository, $this->consultorRepository);
        $service($data);
    }
}