<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Consultor\Admin;

use App\Consultores\Application\Dto\Request\Consultor\ConsultorUpdateRequestAdminDto;
use App\Consultores\Application\Services\Consultor\Admin\ConsultorUpdateByEmailService;
use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Consultores\Domain\ValueObjects\Nivel;
use App\Consultores\Domain\ValueObjects\Perfil;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;

class ConsultorUpdateByEmailServiceTest extends Unit
{
    private ConsultorRepositoryInterface $consultorRepository;
    private HabilidadRepositoryInterface $habilidadRepository;
    private ConsultorUpdateByEmailService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
        $this->service = new ConsultorUpdateByEmailService($this->consultorRepository, $this->habilidadRepository);
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
        $data = new ConsultorUpdateRequestAdminDto(email: 'Consultor@ejemplo.com', perfil: 'project manager');
        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);
        $consultor->method('getPerfil')->willReturn(Perfil::fromString('desarrollador'));

        $consultor->expects($this->once())->method('setPerfil')->with(Perfil::fromString('project manager'));
        $this->consultorRepository->expects($this->once())->method('save')->with($consultor);


        $response = ($this->service)($data);

        $this->assertEquals(['perfil' => 'project manager'], $response->actualizacion);
    }

    public function testShouldUpdateNewHabilidades(): void
    {
        $data = new ConsultorUpdateRequestAdminDto(email: 'Consultor@ejemplo.com',
            habilidades: [['nombre' => 'Python', 'nivel' => 'alto']]);

        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);
        $consultor->method('getHabilidades')->willReturn(new ArrayCollection());

        $habilidadPython = $this->createHabilidadMock('Python', 'alto');

        $this->habilidadRepository->method('getHabilidadesByConsultor')->willReturn([]);
        $this->habilidadRepository->method('validateHabilidad')->willReturn($habilidadPython);
        $this->consultorRepository->expects($this->once())->method('save')->with($consultor);

        $response = ($this->service)($data);
        $this->assertEquals(['habilidades' => $data->habilidades], $response->actualizacion);
    }

    public function testShouldRemoveHabilidades(): void
    {
        $data =  new ConsultorUpdateRequestAdminDto(email: 'Consultor@ejemplo.com',
            borrar_habilidades: [['nombre' => 'Php', 'nivel' => 'alto']]);
        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);

        $habilidadPhp = $this->createHabilidadMock('Php', 'alto', $consultor);

        $this->habilidadRepository->method('getHabilidadesByConsultor')->willReturn([$habilidadPhp]);
        $this->habilidadRepository->method('validateHabilidad')->willReturn($habilidadPhp);
        $this->consultorRepository->expects($this->once())->method('save')->with($consultor);


        $response = ($this->service)($data);

        $this->assertEquals(['removed_habilidades' => $data->borrar_habilidades], $response->actualizacion);
    }

    public function testShouldNotUpdateAnything(): void
    {
        $data = new ConsultorUpdateRequestAdminDto(email: 'Consultor@ejemplo.com');
        $consultor = $this->createConsultorMock();

        $this->consultorRepository->method('validateConsultor')->willReturn($consultor);


        $response = ($this->service)($data);

        $this->assertEquals('No se ha actualizado ningun dato', $response->message);
    }



}