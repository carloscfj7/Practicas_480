<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor\Admin;

use App\Consultores\Application\Dto\Entity\ConsultorDto;
use App\Consultores\Application\Services\Consultor\Admin\ConsultorReadByEmailService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ConsultorReadByEmailServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;
    private ConsultorDto $consultorDto;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->consultorDto = $this->createMock(ConsultorDto::class);
    }

    public function testShouldReadConsultorByEmailSuccesfully(): void
    {
        $data = [
            'email' => 'consultor@ejemplo.com'
        ];

        $consultor = $this->createMock(Consultor::class);

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['email'])
            ->willReturn($consultor);

        $this->consultorDto
            ->expects($this->once())
            ->method('fromEntity')
            ->willReturn($this->consultorDto);

        $service = new ConsultorReadByEmailService($this->consultorRepository, $this->consultorDto);

        $response = $service($data);

        $this->assertEquals($this->consultorDto, $response);

    }
}