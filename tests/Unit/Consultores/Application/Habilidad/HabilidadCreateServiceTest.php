<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadCreateService;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class HabilidadCreateServiceTest extends Unit
{
    private HabilidadRepositoryInterface $habilidadRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
    }


    public function testShouldCreateDisponibildiadSucesfully(): void
    {
        $data = [
            "habilidad" => "Php",
            "nivel" => "medio"
        ];



        $this->habilidadRepository
            ->expects($this->once())
            ->method('save');

        $service = new HabilidadCreateService($this->habilidadRepository);

        $response = $service($data);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertEquals("Habilidad creada correctamente", $content['message']);
    }





}