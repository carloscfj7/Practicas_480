<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor\Admin;

use App\Consultores\Application\Dto\Entity\ConsultorDto;
use App\Consultores\Application\Services\Consultor\Admin\ConsultorReadAllService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsultorReadAllServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;
    private ConsultorDto $consultorDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->consultorDto = $this->createMock(ConsultorDto::class);
    }

    public function testShouldReturnClientesSuccessfully(): void
    {
        $consultor = $this->createMock(Consultor::class);
        $consultor2 = $this->createMock(Consultor::class);
        $consultores = [$consultor, $consultor2];

        $this->consultorRepository
            ->method('getAll')
            ->willReturn($consultores);

        $consultoresFormateados = [
            ['nombre' => 'Consultor 1', 'apellidos' => 'apellidos ', 'perfil' => 'project manager'],
            ['nombre' => 'Cosnultor 2', 'apellidos' => 'apellidos', 'perfil' => 'desarrollador']
        ];

        $this->consultorDto
            ->method('collectionFromEntities')
            ->with($consultores)
            ->willReturn($consultoresFormateados);

        $service = new ConsultorReadAllService($this->consultorRepository, $this->consultorDto);

        $response = $service();

        $this->assertEquals($consultoresFormateados, $response);
    }

    public function testShouldReturnEmptyMessageWhenNoClientesFound(): void
    {
        $this->consultorRepository
            ->method('getAll')
            ->willReturn([]);

        $service = new ConsultorReadAllService($this->consultorRepository, $this->consultorDto);

        $response = $service();


        $this->assertEquals(null, $response);
    }
}