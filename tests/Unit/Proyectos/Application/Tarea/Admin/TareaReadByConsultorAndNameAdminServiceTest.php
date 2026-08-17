<?php

declare(strict_types=1);

namespace App\Tests\Unit\Proyectos\Application\Tarea\Admin;

use App\Consultores\Domain\Consultor;
use App\Consultores\Domain\ConsultorRepositoryInterface;
use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Services\Tareas\Admin\TareaReadByConsultorAndNameAdminService;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByConsultorAndNameAdminServiceTest extends Unit
{
    private TareaRepositoryInterface $tareaRepository;
    private ConsultorRepositoryInterface $consultorRepository;
    private TareaDto $tareaDto;
    private TareaReadByConsultorAndNameAdminService $service;

    protected function setUp(): void
    {
        $this->tareaRepository = $this->createMock(TareaRepositoryInterface::class);
        $this->consultorRepository = $this->createMock(ConsultorRepositoryInterface::class);
        $this->tareaDto = $this->createMock(TareaDto::class);

        $this->service = new TareaReadByConsultorAndNameAdminService(
            $this->tareaRepository,
            $this->consultorRepository,
            $this->tareaDto
        );
    }

    private function createMockConsultor(): Consultor
    {
        return $this->createMock(Consultor::class);
    }

    private function createMockTarea(): Tarea
    {
        return $this->createMock(Tarea::class);
    }



    public function testShouldReturnTareaByConsultorAndNameSuccessfully(): void
    {
        $data = [
            'nombre' => 'Tarea X',
            'consultor' => 'consultor@email.com'
        ];

        $consultor = $this->createMockConsultor();
        $tarea = $this->createMockTarea();

        $this->consultorRepository
            ->expects($this->once())
            ->method('validateConsultor')
            ->with($data['consultor'])
            ->willReturn($consultor);

        $this->tareaRepository
            ->expects($this->once())
            ->method('validateTareaByConsultorAndNombre')
            ->with($data['nombre'], $consultor)
            ->willReturn($tarea);

        $this->tareaDto
            ->expects($this->once())
            ->method('fromEntity')
            ->with($tarea)
            ->willReturn($this->tareaDto);

        $response = ($this->service)($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Estos son los datos de la tarea', $content['message']);
    }
}
