<?php
declare(strict_types=1);

namespace App\Proyectos\Application\Services\Tareas\Admin;

use App\Proyectos\Application\Dto\TareaDto;
use App\Proyectos\Application\Exceptions\Proyecto\ProyectoNotFoundException;
use App\Proyectos\Domain\ProyectoRepositoryInterface;
use App\Proyectos\Domain\TareaRepositoryInterface;
use App\Shared\Application\Exceptions\RequiredDataException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TareaReadByProyectoAdminService
{
    public function __construct(private TareaRepositoryInterface $tareaRepository, private ProyectoRepositoryInterface $proyectoRepository, private TareaDto $tareaDto)
    {
    }

    public function __invoke(array $data)
    {
        $this->validateRequiredData($data);

        $proyecto = $this->proyectoRepository->validateProyectoByNombre($data['proyecto']);

        $tareas = $this->tareaRepository->getTareasByProyecto($proyecto);
        if ($tareas === []) {
            return new JsonResponse(["message" => "El proyecto con nombre ".$proyecto->getNombre()." no tiene ninguna tarea"], Response::HTTP_OK);
        }
        $tareas = $this->tareaDto->collectionFromEntities($tareas);
        return new JsonResponse(['message'=>'Estas son las tareas del proyecto: '.$proyecto->getNombre(),"tareas" => $tareas], Response::HTTP_OK);
    }


    private function validateRequiredData(array $data):void
    {
        if (empty($data['proyecto'])) {
            throw new RequiredDataException();
        }

    }
}