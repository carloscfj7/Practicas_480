<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadUpdateService;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class HabilidadUpdateServiceTest extends Unit
{
    private HabilidadRepositoryInterface $habilidadRepository;


    protected function setUp(): void
    {
        parent::setUp();
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
    }


    private function createMockHabilidad()
    {
        return $this->createMock(Habilidad::class);
    }

    public function testShouldUpdateAllHabilidadSuccessfully(): void
    {
        $data = [
            "nombre" => "Php",
            "nivel" => "medio",
            "nuevo_nombre" => "Python",
            "nuevo_nivel" => "experto"
        ];

        $habilidad = $this->createMockHabilidad();

        $this->habilidadRepository
            ->expects($this->once())
            ->method('validateHabilidad')
            ->with($data)
            ->willReturn($habilidad);

        $habilidad
            ->expects($this->once())
            ->method('setNombre');

        $habilidad
            ->expects($this->once())
            ->method('setNivel');

        $service = new HabilidadUpdateService($this->habilidadRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizacion', $content);

        $this->assertEquals([
            "nombre" => "Python",
            "nivel" => "experto"
        ], $content['actualizacion']);
    }

    public function testShouldUpdateNombreHabilidadSuccessfully(): void
    {
        $data = [
            "nombre" => "Php",
            "nivel" => "medio",
            "nuevo_nombre" => "Python",
        ];

        $habilidad = $this->createMockHabilidad();

        $this->habilidadRepository
            ->expects($this->once())
            ->method('validateHabilidad')
            ->with($data)
            ->willReturn($habilidad);

        $habilidad
            ->expects($this->once())
            ->method('setNombre');



        $service = new HabilidadUpdateService($this->habilidadRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizacion', $content);

        $this->assertEquals([
            "nombre" => "Python",
        ], $content['actualizacion']);
    }

    public function testShouldUpdateNivelHabilidadSuccessfully(): void
    {
        $data = [
            "nombre" => "Php",
            "nivel" => "medio",
            "nuevo_nivel" => "experto",
        ];

        $habilidad = $this->createMockHabilidad();

        $this->habilidadRepository
            ->expects($this->once())
            ->method('validateHabilidad')
            ->with($data)
            ->willReturn($habilidad);

        $habilidad
            ->expects($this->once())
            ->method('setNivel');



        $service = new HabilidadUpdateService($this->habilidadRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey('actualizacion', $content);

        $this->assertEquals([
            "nivel" => "experto",
        ], $content['actualizacion']);
    }

    public function testShouldNotUpdateHabilidadSuccessfully(): void
    {
        $data = [
            "nombre" => "Php",
            "nivel" => "medio",
        ];

        $habilidad = $this->createMockHabilidad();

        $this->habilidadRepository
            ->expects($this->once())
            ->method('validateHabilidad')
            ->with($data)
            ->willReturn($habilidad);

        $service = new HabilidadUpdateService($this->habilidadRepository);
        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals("No se ha actualizado ningun dato", $content['message']);
    }



}