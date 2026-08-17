<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor;

use App\Consultores\Application\Dto\Entity\ConsultorDto;
use App\Consultores\Application\Services\Consultor\ConsultorReadService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\Email;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ConsultorReadServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;
    private ConsultorDto $consultorDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->consultorDto = $this->createMock(ConsultorDto::class);
    }


    public function testShouldReadConsultorSuccessfully()
    {
        $email = new Email('consultor@prueba.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);

        $consultor = $this->createMock(Consultor::class);

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($email)
            ->willReturn($consultor);

        $this->consultorDto
            ->expects($this->once())
            ->method('fromEntity')
            ->willReturn($this->consultorDto);

        $service = new ConsultorReadService($this->consultorRepository, $this->consultorDto);
        $response = $service($usuario);

        $this->assertEquals($this->consultorDto, $response);
    }

}