<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Admin;

use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Domain\Exceptions\Tarea\ExistentTareaException;
use App\Proyectos\Domain\Proyecto;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\Tarea;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoAndNameAdminService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ProyectoRepositoryInterface $proyectoRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(array $data)
    {

        $this->validateRequiredData($data);
        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);

        $tarea = $this->tareaRepository->validateTareaByProyectoAndNombre($data['nombre'], $proyecto);

        $tarea = $this->tareaDto->fromEntity($tarea);
        return new JsonResponse(['message'=>'Estos son los datos de la tarea', "tarea" => $tarea], Response::HTTP_OK);
    }


    private function validateRequiredData(array $data): void
    {
        if (empty($data['nombre']) || empty($data['proyecto'])) {
            throw new RequiredDataException();
        }
    }


}