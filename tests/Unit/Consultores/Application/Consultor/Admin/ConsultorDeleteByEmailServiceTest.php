<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor\Admin;

use App\Consultores\Application\Services\Consultor\Admin\ConsultorDeleteByEmailService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ConsultorDeleteByEmailServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
    }

    public function testShouldDeleteConsultorByEmailSuccessfully(): void
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

        $this->consultorRepository
            ->expects($this->once())
            ->method('remove')
            ->with($consultor);

        $service = new ConsultorDeleteByEmailService($this->consultorRepository);
        $response = $service($data);

        $this->assertEquals('Consultor eliminado correctamente', $response);
    }

}