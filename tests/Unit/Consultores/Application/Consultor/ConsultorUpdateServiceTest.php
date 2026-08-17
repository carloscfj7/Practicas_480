<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorUpdateRequestDto;
use App\Consultores\Application\Services\Consultor\ConsultorUpdateService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Consultores\Domain\ValueObjects\Nivel;
use App\Consultores\Domain\ValueObjects\Perfil;
use App\Usuarios\Domain\Usuario;
use App\Usuarios\Domain\ValueObjects\Email;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;

class ConsultorUpdateServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;
    private HabilidadRepositoryInterface $habilidadRepository;
    private ConsultorUpdateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
        $this->service = new ConsultorUpdateService($this->consultorRepository, $this->habilidadRepository);
    }

    private function createUsuarioMock()
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getEmail')->willReturn(new Email('consultor@example.com'));
        return $usuario;
    }

    private function createConsultorMock()
    {
        return $this->createMock(Consultor::class);
    }

    private function createHabilidadMock(string $nombre, string $nivel, Consultor $consultor = null): Habilidad
    {
        $habilidad = $this->createMock(Habilidad::class);
        $habilidad->method('getNombre')->willReturn($nombre);
        $habilidad->method('getNivel')->willReturn(Nivel::fromString($nivel));
        if ($consultor instanceof \App\Consultores\Domain\Consultor) {
            $habilidad->method('getConsultores')->willReturn(new ArrayCollection([$consultor]));
        }
        return $habilidad;
    }

    public function testShouldUpdatePerfilOnly(): void
    {
        $usuario = $this->createUsuarioMock();
        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);
        $consultor->method('getPerfil')->willReturn(Perfil::fromString('desarrollador'));

        $consultor->expects($this->once())->method('setPerfil')->with(Perfil::fromString('project manager'));
        $this->consultorRepository->expects($this->once())->method('save')->with($consultor);

        $data = new ConsultorUpdateRequestDto(perfil: 'project manager');
        $response = ($this->service)($usuario, $data);

        $this->assertEquals(['perfil' => 'project manager'], $response->actualizacion);
    }

    public function testShouldUpdateNewHabilidades(): void
    {
        $usuario = $this->createUsuarioMock();
        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);
        $consultor->method('getHabilidades')->willReturn(new ArrayCollection());

        $habilidadPython = $this->createHabilidadMock('Python', 'alto');

        $this->habilidadRepository->method('getHabilidadesByConsultor')->willReturn([]);
        $this->habilidadRepository->method('validateHabilidad')->willReturn($habilidadPython);
        $this->consultorRepository->expects($this->once())->method('save')->with($consultor);

        $data = new ConsultorUpdateRequestDto(habilidades: [['nombre' => 'Python', 'nivel' => 'alto']]);
        $response = ($this->service)($usuario, $data);

        $this->assertEquals(['habilidades' => $data->habilidades], $response->actualizacion);
    }


    public function testShouldNotUpdateAnything(): void
    {
        $usuario = $this->createUsuarioMock();
        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);

        $data = new ConsultorUpdateRequestDto();
        $response = ($this->service)($usuario, $data);

        $this->assertEquals('No se ha actualizado ningún dato', $response->message);
    }
}
