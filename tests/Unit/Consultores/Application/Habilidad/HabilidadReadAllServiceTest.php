<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Habilidad;

use App\Consultores\Application\Dto\Entity\HabilidadDto;
use App\Consultores\Application\Services\Habilidad\HabilidadReadAllService;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class HabilidadReadAllServiceTest extends Unit
{
    private HabilidadRepositoryInterface  $habilidadRepository;
    private HabilidadDto $habilidadDto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
        $this->habilidadDto = $this->createMock(HabilidadDto::class);
    }


    private function createMockHabilidad()
    {
        return  $this->createMock(Habilidad::class);
    }

    public function testShouldReadHabilidadByUserSuccessfully(): void
    {

        $habilidades = [$this->createMockHabilidad()];

        $formatedHabilidades=[
            "nombre" => "Python",
            "nivel" => "alto"
        ];

        $this->habilidadRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($habilidades);

        $this->habilidadDto
            ->expects($this->once())
            ->method('collectionFromEntities')
            ->with($habilidades)
            ->willReturn($formatedHabilidades);

        $service = new HabilidadReadAllService($this->habilidadRepository,$this->habilidadDto);
        $response = $service();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("Estas son todas las habilidades disponibles: ", $content['message']);

        $this->assertEquals($formatedHabilidades, $content['habilidades']);
    }


    public function testShouldNotReturnAnyHabilidadByUserSuccessfully():void
    {
        $this->habilidadRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $service = new HabilidadReadAllService($this->habilidadRepository, $this->habilidadDto);
        $response = $service();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("No se encontraron habilidades", $content['message']);
    }
}