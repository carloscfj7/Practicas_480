<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor;

use App\Consultores\Application\Services\Consultor\ConsultorDeleteService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Usuarios\Domain\ValueObjects\Email;
use App\Usuarios\Domain\Usuario;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class ConsultorDeleteServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;

    protected function setUp():void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
    }

    public function testShouldDeleteConsultorSuccessfully():void
    {
        $email = new Email('consultor@ejemplo.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);

        $consultor  = $this->createMock(Consultor::class);
        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($email)
            ->willReturn($consultor);

        $this->consultorRepository
            ->expects($this->once())
            ->method('remove');

        $service = new ConsultorDeleteService($this->consultorRepository);
        $response = $service($usuario);


        $this->assertEquals('Consultor eliminado correctamente', $response);
    }
}