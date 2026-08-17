<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Habilidad;

use App\Consultores\Application\Dto\Entity\HabilidadDto;
use App\Consultores\Application\Services\Habilidad\HabilidadReadFromUserService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\Email;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class HabilidadReadByConsultorServiceTest extends Unit
{
    private HabilidadRepositoryInterface $habilidadRepository;
    private ConsultorRepositoryInterface $consultorRepository;

    private HabilidadDto $habilidadDto;


    protected function setUp(): void
    {
        parent::setUp();
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->habilidadDto = $this->createMock(HabilidadDto::class);
    }

    private function createMockUsuario()
    {
        $email = new Email('consultor@prueba.es');
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn($email);
        return $usuario;
    }

    private function createMockHabilidad()
    {
        return  $this->createMock(Habilidad::class);
    }

    private function createMockConsultor()
    {
        return $this->createMock(Consultor::class);
    }
    public function testShouldReadHabilidadByUserSuccessfully(): void
    {

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);

        $habilidades = [$this->createMockHabilidad()];

        $formatedHabilidades=[
            "nombre" => "Python",
            "nivel" => "alto"
        ];

        $this->habilidadRepository
            ->expects($this->once())
            ->method('getHabilidadesByConsultor')
            ->with($consultor)
            ->willReturn($habilidades);

        $this->habilidadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($habilidades)
            ->willReturn($formatedHabilidades);

        $service = new HabilidadReadFromUserService($this->habilidadRepository, $this->consultorRepository, $this->habilidadDto);
        $response = $service($usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("Estas son las habilidades del consultor con email: consultor@prueba.es", $content['message']);

        $this->assertEquals($formatedHabilidades, $content['habilidades']);
    }


    public function testShouldNotReturnAnyHabilidadByUserSuccessfully():void
    {

        $usuario = $this->createMockUsuario();

        $consultor = $this->createMockConsultor();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($usuario->getEmail()->value())
            ->willReturn($consultor);


        $this->habilidadRepository
            ->expects($this->once())
            ->method('getHabilidadesByConsultor')
            ->with($consultor)
            ->willReturn([]);

        $service = new HabilidadReadFromUserService($this->habilidadRepository, $this->consultorRepository, $this->habilidadDto);
        $response = $service($usuario);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("El consultor con email: consultor@prueba.es no tiene habilidades", $content['message']);
    }
}