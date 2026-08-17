<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultores\Application\Habilidad;

use App\Consultores\Application\Services\Habilidad\HabilidadDeleteService;
use App\Consultores\Domain\Habilidad;
use App\Consultores\Domain\HabilidadRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;

class HabilidadDeleteServiceTest extends Unit
{
    private HabilidadRepositoryInterface $habilidadRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->habilidadRepository = $this->createMock(HabilidadRepositoryInterface::class);
    }

    public function testShouldDeleteHabilidadSuccessfully(): void
    {
        $data = [
            'nombre' => 'PHP',
            'nivel' => 'alto'
        ];
        $habilidad = $this->createMock(Habilidad::class);
        $this->habilidadRepository
            ->expects($this->once())
            ->method('validateHabilidad')
            ->with($data)
            ->willReturn($habilidad);

        $this->habilidadRepository
            ->expects($this->once())
            ->method('remove');
        $service = new HabilidadDeleteService($this->habilidadRepository);
        $response = $service($data);
        $content = json_decode($response->getContent(),true);
        $this->assertEquals('Habilidad eliminada correctamente', $content['message']);
    }


}